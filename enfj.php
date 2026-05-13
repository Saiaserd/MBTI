<?php
/**
 * MBTI 詳細頁 stub
 * 16 個 MBTI 檔（enfj.php, infp.php, ...）格式都一樣，只有 $type 不同。
 *   1. 設定 $type 為當前型號（要對應到 data/mbti_types.php 的 key）
 *   2. views/mbti.view.php 會去 data/mbti_types.php 拿八維順序、介紹文、上下頁
 *
 * 改某型號的內容 → 改 data/mbti_types.php
 * 改版面 → 改 views/mbti.view.php
 */
$type       = 'enfj';
$page_title = strtoupper($type);
$page_css   = ['assets/css/wikimbtiCSS.css'];
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/mbti.view.php';
require __DIR__ . '/includes/footer.php';
