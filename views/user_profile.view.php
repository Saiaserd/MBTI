<?php
/**
 * 已登入時顯示的個人資料卡
 * 由 user.php 載入，需要的變數：$user_name, $user_id
 */

// 拿姓名第一個字當頭像。mb_substr 才能正確處理中文（一般 substr 會切壞 UTF-8）
$initial = mb_substr($user_name, 0, 1, 'UTF-8');
?>
<div class="profile-card">
    <div class="profile-avatar"><?= htmlspecialchars($initial) ?></div>
    <h2 class="profile-name"><?= htmlspecialchars($user_name) ?></h2>
    <p class="profile-id">帳號：<?= htmlspecialchars($user_id) ?></p>
    <div class="profile-actions">
        <a href="index.php" class="btn-action">首頁</a>
        <a href="survey.php" class="btn-action">測驗</a>
        <a href="api/logout.php" class="btn-logout">登出</a>
    </div>
</div>
