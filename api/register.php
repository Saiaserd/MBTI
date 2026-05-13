<?php
/**
 * 註冊處理端點
 * 接收 user.php 註冊表單送來的 name + id + pass，把密碼雜湊後寫入 DB。
 */

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../user.php");
    exit();
}

$id   = $_POST['id']   ?? '';
$name = $_POST['name'] ?? '';
$pass = $_POST['pass'] ?? '';

// ★ 絕對不要把明文密碼存進 DB，一定要 hash 過
// password_hash 自動產生 salt + 用 bcrypt，未來改演算法也不用改驗證程式
$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (id, name, pass) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $id, $name, $hashed_pass);

if ($stmt->execute()) {
    echo "<script>alert('註冊成功！'); location.href='../user.php';</script>";
} else {
    // 常見錯誤：id 重複（PRIMARY KEY 衝突）
    echo "註冊失敗：" . $stmt->error;
}

require_once __DIR__ . '/../includes/close.php';
