<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user.php");
    exit;
}

if (!empty($_POST['session_id'])) {
    $user_id    = $_SESSION['user_id'];
    $session_id = $_POST['session_id'];

    $stmt = $conn->prepare("DELETE FROM chat_logs WHERE user_id = ? AND session_id = ?");
    $stmt->bind_param("ss", $user_id, $session_id);
    $stmt->execute();
}

header("Location: log.php");
require_once 'close.php';
?>
