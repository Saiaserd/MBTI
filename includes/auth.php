<?php
/**
 * 登入狀態 helper 函式
 * 統一所有頁面判斷登入狀態的寫法，以後改判斷邏輯只要改這個檔。
 *
 * 用法：
 *   if (is_logged_in()) { ... }
 *   $uid = current_user_id();
 *   $name = current_user_name();
 */

// 有沒有登入？ login.php 成功時會寫 $_SESSION['user_id']
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

// 目前登入的帳號 id，沒登入回 null
function current_user_id(): ?string {
    return $_SESSION['user_id'] ?? null;
}

// 目前登入的顯示名稱，沒登入回 null
function current_user_name(): ?string {
    return $_SESSION['user_name'] ?? null;
}
