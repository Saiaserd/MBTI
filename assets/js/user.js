document.addEventListener('DOMContentLoaded', function () {
    const loginBtn = document.getElementById('show-login');
    const registerBtn = document.getElementById('show-register');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    registerBtn.addEventListener('click', function () {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        registerBtn.classList.add('active');
        loginBtn.classList.remove('active');
    });

    loginBtn.addEventListener('click', function () {
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
        loginBtn.classList.add('active');
        registerBtn.classList.remove('active');
    });
});
