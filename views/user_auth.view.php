<?php
/**
 * 未登入時顯示的登入/註冊表單
 * 兩個表單疊在一起，用 user.js 切換顯示。
 */
?>
<div class="auth-container">
    <div class="auth-card">
        <!-- 切換按鈕（哪個有 .active 就是當前顯示的表單） -->
        <div class="auth-toggle">
            <button id="show-login" class="active">登入帳號</button>
            <button id="show-register">註冊新帳號</button>
        </div>

        <!-- 登入表單：送到 api/login.php -->
        <form id="login-form" class="auth-form" action="api/login.php" method="POST">
            <h3>帳號登入</h3>
            <div class="input-group">
                <label>帳號</label>
                <input type="text" name="id" placeholder="請輸入帳號 (如: estj_1234)" required>
            </div>
            <div class="input-group">
                <label>密碼</label>
                <input type="password" name="pass" placeholder="請輸入您的密碼" required>
            </div>
            <button type="submit" class="btn-primary">登入</button>
        </form>

        <!-- 註冊表單：送到 api/register.php，預設隱藏 -->
        <form id="register-form" class="auth-form" action="api/register.php" method="POST" style="display: none;">
            <h3>註冊帳號</h3>
            <div class="input-group">
                <label>姓名</label>
                <input type="text" name="name" placeholder="您的姓名" required>
            </div>
            <div class="input-group">
                <label>帳號代碼</label>
                <input type="text" name="id" placeholder="請輸入帳號 (如: adijer_0123)" required>
            </div>
            <div class="input-group">
                <label>設定密碼</label>
                <input type="password" name="pass" placeholder="至少 8 個字元" required>
            </div>
            <button type="submit" class="btn-primary">完成註冊</button>
        </form>
    </div>
</div>

<script src="assets/js/user.js"></script>
