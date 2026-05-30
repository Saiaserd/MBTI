<?php
/**
 * 八維測驗結果處理端點
 *
 * 流程：
 *   1. 收 assessment.php 表單 POST 過來的 70 個 q{id} = 1~5
 *   2. 用 includes/scoring.php 算出 8 維分數 + MBTI 類型
 *   3. 寫入 $_SESSION['assessment']（訪客也用這個，session 結束就消失）
 *   4. 若使用者登入 → 另存一筆到 assessments 資料表
 *   5. 重導到 assessment_result.php 顯示結果
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/scoring.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../assessment.php');
    exit;
}

// ---------- 1. 收答案：POST 裡所有 q1~q70 ----------
$questions = require __DIR__ . '/../data/questions.php';
$answers   = [];
foreach ($questions as $q) {
    $key = 'q' . $q['id'];
    if (!isset($_POST[$key])) {
        // 缺題就把使用者導回 assessment 重做（不該發生，前端有擋）
        header('Location: ../assessment.php?error=incomplete');
        exit;
    }
    $answers[$q['id']] = (int)$_POST[$key];
}

// ---------- 2. 算分 ----------
$report = build_assessment_report($answers);

// ---------- 3. 登入者先寫 DB，拿到 assessment.id（要在寫入 session 前先取到 id）----------
$assessment_id = null;  // 訪客為 null
if (is_logged_in()) {
    require __DIR__ . '/../includes/db.php';

    $uid    = current_user_id();
    $s      = $report['scores'];
    $type   = $report['type'];

    $stmt = $conn->prepare(
        "INSERT INTO assessments
           (user_id, ni_score, ne_score, si_score, se_score,
            ti_score, te_score, fi_score, fe_score, mbti_type)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "siiiiiiiis",
        $uid,
        $s['Ni'], $s['Ne'], $s['Si'], $s['Se'],
        $s['Ti'], $s['Te'], $s['Fi'], $s['Fe'],
        $type
    );
    $stmt->execute();

    // 之後存聊天紀錄會用這個 id 綁起來（紀錄頁要按報告分組）
    $assessment_id = $conn->insert_id;

    require_once __DIR__ . '/../includes/close.php';
}

// ---------- 4. 寫入 session（訪客 + 登入者都會寫）----------
$_SESSION['assessment'] = [
    'id'         => $assessment_id,            // 訪客為 null，登入者為 assessments.id
    'scores'     => $report['scores'],
    'type'       => $report['type'],
    'stack'      => $report['stack'],
    'desc'       => $report['desc'],
    'created_at' => date('Y-m-d H:i:s'),
];

// ---------- 5. 重導去看報告 ----------
header('Location: ../assessment_result.php');
exit;
