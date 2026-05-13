<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/db.php';

$logged_in    = is_logged_in();
$log_sessions = [];

if ($logged_in) {
    $user_id = current_user_id();

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
require __DIR__ . '/views/log.view.php';
require __DIR__ . '/includes/footer.php';

require_once __DIR__ . '/includes/close.php';
