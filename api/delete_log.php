<?php
/**
 * 刪除對話端點
 * log.php 列表中每筆對話都有一個「刪除」表單，POST 過來這裡。
 * 刪完導回 log.php。
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/db.php';

if (!is_logged_in()) {
    header("Location: ../user.php");
    exit;
}

if (!empty($_POST['session_id'])) {
    $user_id    = current_user_id();
    $session_id = $_POST['session_id'];

    // ★ WHERE 條件要同時比對 user_id，避免有人改 session_id 去刪別人的對話
    $stmt = $conn->prepare("DELETE FROM chat_logs WHERE user_id = ? AND session_id = ?");
    $stmt->bind_param("ss", $user_id, $session_id);
    $stmt->execute();
}

header("Location: ../log.php");
require_once __DIR__ . '/../includes/close.php';
