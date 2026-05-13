<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = '使用者帳號管理';
$page_css   = ['assets/css/userCSS.css'];

require __DIR__ . '/includes/header.php';

if (is_logged_in()) {
    $user_name = current_user_name();
    $user_id   = current_user_id();
    require __DIR__ . '/views/user_profile.view.php';
} else {
    require __DIR__ . '/views/user_auth.view.php';
}

require __DIR__ . '/includes/footer.php';
