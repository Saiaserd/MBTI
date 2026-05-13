<?php
/**
 * 關閉資料庫連線
 * 一支腳本結束前 require 這個檔。檢查 $conn 存在才關，避免重複關閉報錯。
 */

if (isset($conn)) {
    $conn->close();
}
