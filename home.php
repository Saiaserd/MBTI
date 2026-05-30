<?php
/**
 * 主頁（網站介紹）
 * 純靜態頁面，沒有 PHP 邏輯，只是設定標題跟 CSS 後載入 view。
 */
$page_title = 'MBTI 主頁';
$page_css   = ['assets/css/homeCSS.css'];
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/home.view.php';
require __DIR__ . '/includes/footer.php';
