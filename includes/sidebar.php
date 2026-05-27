<?php
/**
 * 頂部導覽列
 * 由 header.php 自動載入，不用每個頁面手動 include。
 * 想改選單項目（新增頁面、改文字、改 emoji）就改下面的 <ul>。
 * 漢堡按鈕在窄畫面才會顯示，邏輯在 assets/js/sidebar.js。
 */
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="topnav" id="topnav">
    <div class="topnav-brand">
        <h3><i class="fas fa-code"></i> <span>MBTI</span></h3>
    </div>  

    <ul class="topnav-links" id="topnav-links">
        <li><a href="home.php"><i class="user-icon">🏠</i><span>主頁</span></a></li>
        <li><a href="assessment.php" class="nav-highlight"><i class="user-icon">📝</i><span>測驗</span></a></li>
        <li><a href="index.php"><i class="user-icon">🗞️</i><span>八維介紹</span></a></li>
        <li><a href="wiki.php"><i class="user-icon">📖</i><span>基礎介紹</span></a></li>
        <li><a href="seizure.php"><i class="user-icon">📚</i><span>榮格介紹</span></a></li>
        <li><a href="log.php"><i class="user-icon">⌛</i><span>紀錄</span></a></li>
        <li><a href="user.php"><i class="user-icon">👤</i><span>帳號</span></a></li>
        <li><a href="survey.php"><i class="user-icon">🧾</i><span>意見回饋</span></a></li>
    </ul>

    <!-- 漢堡按鈕：只在窄畫面顯示，點擊會 toggle .open 來展開選單 -->
    <button id="toggle-btn" class="topnav-toggle" aria-label="切換選單"><i class="fas fa-bars"></i></button>
</nav>

<script src="assets/js/sidebar.js"></script>
