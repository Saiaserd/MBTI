<?php
/**
 * 資料庫連線工廠（只定義函式，require 本檔不會真的連線）。
 *
 * db_connect() 回傳一個共用的 mysqli 連線（單例）：
 *   - 第一次呼叫才真正連線，之後重複使用同一條連線。
 *   - 內容讀取層（repo.php）與既有的 $conn 都走這支，全站只開一條連線。
 *
 * XAMPP 預設：root 帳號 + 無密碼，資料庫名為 mbti。
 */
function db_connect(): mysqli {
    static $conn = null;
    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = new mysqli('localhost', 'root', '', 'mbti');
    if ($conn->connect_error) {
        die("連線失敗: " . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
