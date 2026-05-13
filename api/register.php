<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../user.php");
    exit();
}

$id   = $_POST['id']   ?? '';
$name = $_POST['name'] ?? '';
$pass = $_POST['pass'] ?? '';

$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (id, name, pass) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $id, $name, $hashed_pass);

if ($stmt->execute()) {
    echo "<script>alert('註冊成功！'); location.href='../user.php';</script>";
} else {
    echo "註冊失敗：" . $stmt->error;
}

require_once __DIR__ . '/../includes/close.php';
