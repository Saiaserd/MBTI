<?php
/**
 * MBTI 詳細頁 stub
 * 16 個 MBTI 檔（enfj.php, infp.php, ...）格式都一樣，只有 $type 不同。
 *   1. 設定 $type 為當前型號（要對應到 mbti_types 資料表的 code）
 *   2. views/mbti.view.php 會透過 content_repo 拿八維順序、介紹文、上下頁
 *
 * 改某型號的內容 → 改 MySQL mbti_types 表（或 sql/content_seed.sql 後重新匯入）
 * 改版面 → 改 views/mbti.view.php
 */
$type       = 'enfj';
$page_title = strtoupper($type);
$page_css   = ['assets/css/wikimbtiCSS.css'];
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/views/mbti.view.php';
require __DIR__ . '/includes/footer.php';
