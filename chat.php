<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/db.php';

$is_continue     = false;
$loaded_history  = [];
$chat_session_id = bin2hex(random_bytes(16));

if (!empty($_GET['session_id']) && is_logged_in()) {
    $chat_session_id = $_GET['session_id'];
    $is_continue     = true;
    $uid             = current_user_id();

    $stmt = $conn->prepare("SELECT user_message, ai_response FROM chat_logs WHERE user_id = ? AND session_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("ss", $uid, $chat_session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $loaded_history[] = $row;
    }
}

$page_title      = '聊天主頁';
$page_css        = ['assets/css/chatCSS.css?v=2'];
$page_extra_head =
    '<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>' .
    '<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/chat.view.php';
require __DIR__ . '/includes/footer.php';

require_once __DIR__ . '/includes/close.php';
