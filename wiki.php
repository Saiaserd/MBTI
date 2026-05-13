<?php
/**
 * 16 型人格總覽
 * 顯示 16 張卡片，點擊跳到對應的 MBTI 詳細頁（enfj.php / intp.php / ...）。
 * 卡片用迴圈產生，不用 16 個 hard-code 的 <a>。
 */
$page_title = '介紹主頁';
$page_css   = ['assets/css/wikiCSS.css'];
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/wiki.view.php';
require __DIR__ . '/includes/footer.php';
