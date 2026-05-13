<?php
/**
 * 資料庫連線
 * 任何需要查 DB 的檔案就 require 這個檔，使用後得到 $conn 變數。
 * XAMPP 預設：root 帳號 + 沒有密碼，資料庫名為 mbti。
 */

$host     = 'localhost';
$user     = 'root';
$password = '';
$dbname   = 'mbti';

$conn = new mysqli($host, $user, $password, $dbname);

// 連不上 DB 直接停掉整個請求並顯示原因
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

// 設定 UTF-8 才能正常存取中文（沒設定可能會看到亂碼）
$conn->set_charset("utf8mb4");
