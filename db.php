<?php
$host = 'localhost';
$user = 'root';
$password = ''; // XAMPP 預設為空
$dbname = 'mbti';

$conn = new mysqli($host, $user, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 設定編碼避免中文亂碼
$conn->set_charset("utf8mb4");
?>