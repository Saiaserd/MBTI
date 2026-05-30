<?php
/**
 * 紀錄頁（按報告分組版）
 *
 * 結構（從上到下）：
 *   - 每筆測驗報告卡（顯示類型 + 8 維分數 + 做測驗時間）
 *     └ 該報告底下的所有聊天串（可繼續對話 / 刪除）
 *   - 「未綁定報告的舊聊天」區塊：給 assessment_id 為 NULL 的舊資料
 *
 * 沒登入 → 提示去登入
 * 登入但沒任何紀錄 → 引導去 home / assessment
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/db.php';

$logged_in   = is_logged_in();
$assessments = [];   // [{id, mbti_type, created_at, scores, chat_groups: [{session_id, started_at, messages}, ...]}, ...]
$orphan_chats = [];  // 沒綁報告的舊聊天 [{session_id, started_at, messages}, ...]

if ($logged_in) {
    $uid = current_user_id();

    /* ---------- 1. 抓出這個使用者的所有測驗報告 ---------- */
    $stmt = $conn->prepare(
        "SELECT id, ni_score, ne_score, si_score, se_score,
                ti_score, te_score, fi_score, fe_score,
                mbti_type, created_at
           FROM assessments
          WHERE user_id = ?
          ORDER BY created_at DESC"
    );
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $rs = $stmt->get_result();
    while ($a = $rs->fetch_assoc()) {
        $assessments[$a['id']] = [
            'id'         => (int)$a['id'],
            'mbti_type'  => $a['mbti_type'],
            'created_at' => $a['created_at'],
            'scores'     => [
                'Ni' => (int)$a['ni_score'], 'Ne' => (int)$a['ne_score'],
                'Si' => (int)$a['si_score'], 'Se' => (int)$a['se_score'],
                'Ti' => (int)$a['ti_score'], 'Te' => (int)$a['te_score'],
                'Fi' => (int)$a['fi_score'], 'Fe' => (int)$a['fe_score'],
            ],
            'chat_groups' => [],
        ];
    }

    /* ---------- 2. 抓出所有聊天串（依 session_id 分組） ----------
       一次查所有訊息，按 session_id 分桶。順便取每串的 assessment_id（同一串綁同一個報告）。
       這比針對每串獨立查更快，因為 70 題報告 + 數十條聊天的資料量不大。
    */
    $stmt = $conn->prepare(
        "SELECT session_id, assessment_id, user_message, ai_response, created_at
           FROM chat_logs
          WHERE user_id = ?
          ORDER BY created_at ASC"
    );
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $rs = $stmt->get_result();

    $sessions_by_id = [];   // session_id => { assessment_id, started_at, messages[] }
    while ($r = $rs->fetch_assoc()) {
        $sid = $r['session_id'];
        if (!isset($sessions_by_id[$sid])) {
            $sessions_by_id[$sid] = [
                'session_id'    => $sid,
                'assessment_id' => $r['assessment_id'] ? (int)$r['assessment_id'] : null,
                'started_at'    => $r['created_at'],
                'messages'      => [],
            ];
        }
        $sessions_by_id[$sid]['messages'][] = [
            'user_message' => $r['user_message'],
            'ai_response'  => $r['ai_response'],
        ];
    }

    /* ---------- 3. 把聊天串掛到對應的報告下 ---------- */
    foreach ($sessions_by_id as $sess) {
        if ($sess['assessment_id'] && isset($assessments[$sess['assessment_id']])) {
            $assessments[$sess['assessment_id']]['chat_groups'][] = $sess;
        } else {
            // 沒綁報告（舊資料）→ 進孤兒區
            $orphan_chats[] = $sess;
        }
    }

    // 每組內的聊天按時間倒序（最新對話排前面）
    foreach ($assessments as &$a) {
        usort($a['chat_groups'], fn($x, $y) => strcmp($y['started_at'], $x['started_at']));
    }
    unset($a);
    usort($orphan_chats, fn($x, $y) => strcmp($y['started_at'], $x['started_at']));
}

$page_title = '紀錄';
$page_css   = ['assets/css/logCSS.css'];

require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/log.view.php'; // view 用 $logged_in, $assessments, $orphan_chats
require __DIR__ . '/includes/footer.php';

require_once __DIR__ . '/includes/close.php';
