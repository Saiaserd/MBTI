<?php
$title    = $page_title ?? 'MBTI';
$css_list = $page_css ?? [];
$extra_head = $page_extra_head ?? '';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="assets/css/sidebarCSS.css">
    <?php foreach ($css_list as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>
    <?= $extra_head ?>
</head>
<body>
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
