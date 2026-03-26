<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>使用者帳號管理</title>
    <link rel="stylesheet" href="css/sidebarCSS.css">
    <link rel="stylesheet" href="css/userCSS.css">
</head>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginBtn = document.getElementById('show-login');
        const registerBtn = document.getElementById('show-register');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        // 點擊「註冊新帳號」按鈕
        registerBtn.addEventListener('click', function() {
            loginForm.style.display = 'none';      // 隱藏登入
            registerForm.style.display = 'block';  // 顯示註冊
            registerBtn.classList.add('active');   // 切換按鈕樣式
            loginBtn.classList.remove('active');
        });

        // 點擊「登入帳號」按鈕
        loginBtn.addEventListener('click', function() {
            registerForm.style.display = 'none';   // 隱藏註冊
            loginForm.style.display = 'block';     // 顯示登入
            loginBtn.classList.add('active');      // 切換按鈕樣式
            registerBtn.classList.remove('active');
        });
    });
</script>

<?php
session_start();
if (isset($_SESSION['user_name'])) {
    $name = htmlspecialchars($_SESSION['user_name']);
    $id   = htmlspecialchars($_SESSION['user_id']);
    $initial = mb_substr($name, 0, 1, 'UTF-8');
    include 'sidebar.html';
    echo "
    <main class='main-content'>
        <div class='profile-card'>
            <div class='profile-avatar'>$initial</div>
            <h2 class='profile-name'>$name</h2>
            <p class='profile-id'>帳號：$id</p>
            <div class='profile-actions'>
                <a href='index.php' class='btn-action'>首頁</a>
                <a href='survey.php' class='btn-action'>測驗</a>
                <a href='logout.php' class='btn-logout'>登出</a>
            </div>
        </div>
    </main>";
    exit();
}
?>

<body>

    <?php include 'sidebar.html'; ?>

    <main class="main-content">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-toggle">
                    <button id="show-login" class="active">登入帳號</button>
                    <button id="show-register">註冊新帳號</button>
                </div>

                <form id="login-form" class="auth-form" action="lp.php" method="POST">
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

                <form id="register-form" class="auth-form" action="rp.php" method="POST" style="display: none;">
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
    </main>

</body>
</html>