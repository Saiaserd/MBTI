<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/db.php';

if (!is_logged_in()) {
    header("Location: ../user.php");
    exit;
}

if (!empty($_POST['session_id'])) {
    $user_id    = current_user_id();
    $session_id = $_POST['session_id'];

    $stmt = $conn->prepare("DELETE FROM chat_logs WHERE user_id = ? AND session_id = ?");
    $stmt->bind_param("ss", $user_id, $session_id);
    $stmt->execute();
}

header("Location: ../log.php");
require_once __DIR__ . '/../includes/close.php';
