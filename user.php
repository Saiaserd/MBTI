<?php
/**
 * 使用者帳號頁
 * 已登入：顯示使用者資料卡（user_profile.view.php）
 * 未登入：顯示登入/註冊表單（user_auth.view.php）
 */

require_once __DIR__ . '/includes/bootstrap.php';

$page_title = '使用者帳號管理';
$page_css   = ['assets/css/userCSS.css'];

require __DIR__ . '/includes/header.php';

if (is_logged_in()) {
    // profile view 會用到這兩個變數
    $user_name = current_user_name();
    $user_id   = current_user_id();
    require __DIR__ . '/views/user_profile.view.php';
} else {
    require __DIR__ . '/views/user_auth.view.php';
}

require __DIR__ . '/includes/footer.php';
