/**
 * 登入/註冊表單切換
 * 兩個表單在 user_auth.view.php 疊在一起，這個檔負責點按鈕時切換顯示。
 */
document.addEventListener('DOMContentLoaded', function () {
    const loginBtn = document.getElementById('show-login');
    const registerBtn = document.getElementById('show-register');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    // 切到註冊表單
    registerBtn.addEventListener('click', function () {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        registerBtn.classList.add('active');
        loginBtn.classList.remove('active');
    });

    // 切回登入表單
    loginBtn.addEventListener('click', function () {
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
        loginBtn.classList.add('active');
        registerBtn.classList.remove('active');
    });
});
