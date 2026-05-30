<?php
/**
 * 共用啟動腳本
 * 每個進入頁第一行 require 這個檔，做兩件事：
 *   1. 啟動 session（讓 $_SESSION 能用）
 *   2. 載入登入狀態的 helper 函式
 */

// 已經 start 過就不要再 start（避免 PHP 警告）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 專案根目錄的絕對路徑，方便其他地方需要時引用
define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require_once __DIR__ . '/auth.php';
