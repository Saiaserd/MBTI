<?php
session_start(); // 1. 必須先啟動 Session 才能進行清除

// 2. 清空所有 Session 變數
$_SESSION = array();

// 3. 如果是用 Cookie 記錄 Session ID，也建議一併清除（更安全）
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. 徹底銷毀 Session
session_destroy();

// 5. 登出後重新導向（導回首頁或登入頁）
header("Location: index.php"); 
exit();
?>