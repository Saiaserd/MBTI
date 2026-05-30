<?php
/**
 * 登出處理端點
 * 徹底清掉 session（變數 + cookie + 伺服器端紀錄），再導回首頁。
 */

session_start(); // 要先 start 才能對現有 session 動手

// 1. 清空 $_SESSION 陣列
$_SESSION = [];

// 2. 把瀏覽器端的 session cookie 也清掉（不然舊的 session id 還會留著）
// 設定 expire = 過去時間 = 通知瀏覽器立刻刪掉
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. 銷毀伺服器端的 session 檔
session_destroy();

header("Location: ../index.php");
exit();
