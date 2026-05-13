<?php
/**
 * 登入處理端點
 * 接收 user.php 登入表單送來的 id + pass，驗證成功就寫入 session 並轉址。
 */

require_once __DIR__ . '/../includes/bootstrap.php'; // 啟動 session
require_once __DIR__ . '/../includes/db.php';        // 拿到 $conn

// 只接受 POST，網址列直接打開這個檔會被導回登入頁
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../user.php");
    exit();
}

$id   = $_POST['id']   ?? '';
$pass = $_POST['pass'] ?? '';

// 用 prepared statement 防 SQL injection
$stmt = $conn->prepare("SELECT name, pass FROM users WHERE id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // password_verify 會自動跟 DB 裡的雜湊比對（DB 存的是 hash，不是明文）
    if (password_verify($pass, $user['pass'])) {
        $_SESSION['user_id']   = $id;
        $_SESSION['user_name'] = $user['name'];
        header("Location: ../index.php");
        exit();
    } else {
        // 用 JS alert + history.back() 讓使用者退回剛剛的表單，密碼欄會被瀏覽器清掉
        echo "<script>alert('密碼錯誤！'); history.back();</script>";
    }
} else {
    echo "<script>alert('找不到帳號！'); history.back();</script>";
}

require_once __DIR__ . '/../includes/close.php';
