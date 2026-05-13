<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../user.php");
    exit();
}

$id   = $_POST['id']   ?? '';
$pass = $_POST['pass'] ?? '';

$stmt = $conn->prepare("SELECT name, pass FROM users WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($pass, $user['pass'])) {
        $_SESSION['user_id']   = $id;
        $_SESSION['user_name'] = $user['name'];
        header("Location: ../index.php");
        exit();
    } else {
        echo "<script>alert('密碼錯誤！'); history.back();</script>";
    }
} else {
    echo "<script>alert('找不到帳號！'); history.back();</script>";
}

require_once __DIR__ . '/../includes/close.php';
