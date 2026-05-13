<?php
/**
 * 側邊欄
 * 由 header.php 自動載入，不用每個頁面手動 include。
 * 想改選單項目（新增頁面、改文字、改 emoji）就改下面的 <ul>。
 * 收合按鈕的 JS 邏輯在 assets/js/sidebar.js。
 */
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-code"></i> <span>MBTI</span></h3>
        <!-- 點這個按鈕會 toggle .collapsed 來收合側邊欄 -->
        <button id="toggle-btn"><i class="fas fa-bars"></i></button>
    </div>

    <ul class="nav-links">
        <li><a href="home.php"><i class="user-icon">🏠</i><span>主頁</span></a></li>
        <li><a href="chat.php"><i class="user-icon">🔧</i><span>AI聊天</span></a></li>
        <li><a href="index.php"><i class="user-icon">🗞️</i><span>八維介紹</span></a></li>
        <li><a href="wiki.php"><i class="user-icon">📖</i><span>基礎介紹</span></a></li>
        <li><a href="seizure.php"><i class="user-icon">📚</i><span>榮格介紹</span></a></li>
        <li><a href="log.php"><i class="user-icon">⌛</i><span>紀錄</span></a></li>
        <li><a href="user.php"><i class="user-icon">👤</i><span>帳號</span></a></li>
        <li><a href="survey.php"><i class="user-icon">🧾</i><span>問卷</span></a></li>
    </ul>
</nav>

<script src="assets/js/sidebar.js"></script>
