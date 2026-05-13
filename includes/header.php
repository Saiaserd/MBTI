<?php
/**
 * 共用頁首
 * 每個進入頁 require 這個檔，會印出 <!DOCTYPE> ~ <body> ~ 側邊欄。
 *
 * 進入頁在 require 之前可以設定下面這些變數：
 *   $page_title       → 瀏覽器分頁標題
 *   $page_css         → 這頁要額外載入的 CSS 陣列，例如 ['assets/css/homeCSS.css']
 *   $page_extra_head  → 要塞進 <head> 的額外 HTML（例如外部 JS 函式庫）
 */

// ?? 是 null coalesce：變數沒設時用預設值
$title      = $page_title ?? 'MBTI';
$css_list   = $page_css ?? [];
$extra_head = $page_extra_head ?? '';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>

    <!-- 所有頁面都載入側邊欄樣式 -->
    <link rel="stylesheet" href="assets/css/sidebarCSS.css">

    <!-- 每頁自己宣告的額外 CSS -->
    <?php foreach ($css_list as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>

    <?= $extra_head ?>
</head>
<body>
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
