<?php
/**
 * 聊天紀錄頁
 * 沒登入 → 提示去登入
 * 有登入 → 撈出這個使用者的所有對話串（按開始時間排序），每串再撈出全部訊息
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/db.php';

$logged_in    = is_logged_in();
$log_sessions = [];

if ($logged_in) {
    $user_id = current_user_id();

    // 步驟 1：列出這個使用者所有「對話串」（用 session_id 分組），順便取每串的開始時間
    // 用 GROUP BY 把同一 session_id 的訊息收起來，MIN(created_at) 是這串的最早一句
    $stmt = $conn->prepare("
        SELECT session_id, MIN(created_at) AS started_at
        FROM chat_logs
        WHERE user_id = ?
        GROUP BY session_id
        ORDER BY started_at DESC
    ");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $sessions = $stmt->get_result();

    // 步驟 2：對每串對話再撈出裡面所有訊息
    // 把 prepare 拉到 while 外面，重複用同一個 statement 比較有效率
    $msg_stmt = $conn->prepare("SELECT user_message, ai_response FROM chat_logs WHERE user_id = ? AND session_id = ? ORDER BY created_at ASC");

    while ($session = $sessions->fetch_assoc()) {
        $sid = $session['session_id'];
        $msg_stmt->bind_param("ss", $user_id, $sid);
        $msg_stmt->execute();
        $msg_result = $msg_stmt->get_result();

        $messages = [];
        while ($m = $msg_result->fetch_assoc()) {
            $messages[] = $m;
        }

        // 組好的格式：每串對話 = [session_id, 開始時間, 訊息陣列]
        $log_sessions[] = [
            'session_id' => $sid,
            'started_at' => $session['started_at'],
            'messages'   => $messages,
        ];
    }
}

$page_title = '聊天紀錄';
$page_css   = ['assets/css/logCSS.css'];

require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/log.view.php'; // view 會用 $logged_in 跟 $log_sessions
require __DIR__ . '/includes/footer.php';

require_once __DIR__ . '/includes/close.php';
