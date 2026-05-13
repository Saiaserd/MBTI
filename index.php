<?php
/**
 * 八維介紹頁
 * 顯示 8 個榮格認知功能的卡片，點擊會彈出 modal 顯示詳細解釋。
 * 卡片資料 + modal 邏輯都在 assets/js/index.js。
 */
$page_title = '主頁';
$page_css   = ['assets/css/indexCSS.css'];
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/index.view.php';
require __DIR__ . '/includes/footer.php';
