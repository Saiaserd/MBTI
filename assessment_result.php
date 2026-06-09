<?php
/**
 * 測驗結果 + AI 聊天（同一頁）
 *
 * 三種進入方式：
 *   1. 剛測完 → 用 $_SESSION['assessment']（訪客也走這條）
 *   2. ?assessment_id=N → 從紀錄頁回看舊報告（需登入，會驗證 user_id）
 *   3. ?session_id=xxx → 從紀錄頁繼續舊對話（會自動回查那輪對話綁的 assessment_id）
 *
 * 行為：
 *   - 沒測驗結果 + 沒參數 → 導去 assessment.php
 *   - 訪客存取 ?xxx → 拒絕（沒登入沒紀錄可看）
 */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/content_repo.php';

$assessment      = null;
$chat_session_id = null;
$is_continue     = false;
$loaded_history  = [];

// ---------- 模式 1：繼續舊對話 ----------
if (!empty($_GET['session_id']) && is_logged_in()) {
    require_once __DIR__ . '/includes/db.php';

    $uid             = current_user_id();
    $chat_session_id = $_GET['session_id'];
    $is_continue     = true;

    // 撈這串對話 + 該對話綁的 assessment_id
    $stmt = $conn->prepare(
        "SELECT user_message, ai_response, assessment_id
           FROM chat_logs
          WHERE user_id = ? AND session_id = ?
          ORDER BY created_at ASC"
    );
    $stmt->bind_param("ss", $uid, $chat_session_id);
    $stmt->execute();
    $rs = $stmt->get_result();

    $bound_assessment_id = null;
    while ($row = $rs->fetch_assoc()) {
        $loaded_history[] = [
            'user_message' => $row['user_message'],
            'ai_response'  => $row['ai_response'],
        ];
        if ($bound_assessment_id === null && $row['assessment_id']) {
            $bound_assessment_id = $row['assessment_id'];
        }
    }

    if ($bound_assessment_id) {
        $assessment = load_assessment_from_db($conn, $uid, $bound_assessment_id);
    }
}

// ---------- 模式 2：直接看某筆舊報告 ----------
if (!$assessment && !empty($_GET['assessment_id']) && is_logged_in()) {
    require_once __DIR__ . '/includes/db.php';
    $assessment = load_assessment_from_db($conn, current_user_id(), (int)$_GET['assessment_id']);
}

// ---------- 模式 3（預設）：用 $_SESSION 裡剛測完的報告 ----------
if (!$assessment && !empty($_SESSION['assessment'])) {
    $assessment = $_SESSION['assessment'];
}

// 都沒拿到報告 → 導回測驗
if (!$assessment) {
    header('Location: assessment.php');
    exit;
}

// 防呆：登入者 session 裡的報告若缺 id（改版前的舊 session 會發生），
// 從 DB 抓「這個 user 最新一筆 assessment」補回去，避免之後的對話又寫成 orphan
if (is_logged_in() && empty($assessment['id'])) {
    require_once __DIR__ . '/includes/db.php';
    $uid  = current_user_id();
    $stmt = $conn->prepare(
        "SELECT id FROM assessments WHERE user_id = ? ORDER BY created_at DESC LIMIT 1"
    );
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    if ($row = $stmt->get_result()->fetch_assoc()) {
        $assessment['id'] = (int)$row['id'];
    }
}

// 沒從紀錄頁進來時，每次刷頁都產生新的 chat_session_id（= 新對話）
if (!$chat_session_id) {
    $chat_session_id = bin2hex(random_bytes(16));
}

// 同步 $_SESSION['assessment']，讓 api/chat.php 拿得到當前看的報告
$_SESSION['assessment'] = $assessment;

$page_title = '你的認知功能報告';
$page_css   = [
    'assets/css/chatCSS.css?v=2',
    'assets/css/assessment_resultCSS.css',
];
$page_extra_head =
    '<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>' .
    '<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/assessment_result.view.php';
require __DIR__ . '/includes/footer.php';


/**
 * 從 DB 撈一筆 assessment（驗證 user_id 確保不會看到別人的）
 * 回傳格式跟 $_SESSION['assessment'] 一致，讓 view 不用區分來源。
 */
function load_assessment_from_db(mysqli $conn, string $user_id, int $assessment_id): ?array {
    $stmt = $conn->prepare(
        "SELECT id, ni_score, ne_score, si_score, se_score,
                ti_score, te_score, fi_score, fe_score,
                mbti_type, created_at
           FROM assessments
          WHERE id = ? AND user_id = ?
          LIMIT 1"
    );
    $stmt->bind_param("is", $assessment_id, $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) return null;

    $types = get_mbti_types();
    $key   = strtolower($row['mbti_type']);

    return [
        'id'         => (int)$row['id'],
        'scores'     => [
            'Ni' => (int)$row['ni_score'], 'Ne' => (int)$row['ne_score'],
            'Si' => (int)$row['si_score'], 'Se' => (int)$row['se_score'],
            'Ti' => (int)$row['ti_score'], 'Te' => (int)$row['te_score'],
            'Fi' => (int)$row['fi_score'], 'Fe' => (int)$row['fe_score'],
        ],
        'type'       => $row['mbti_type'],
        'stack'      => $types[$key]['functions'] ?? [],
        'desc'       => $types[$key]['desc'] ?? '',
        'created_at' => $row['created_at'],
    ];
}
