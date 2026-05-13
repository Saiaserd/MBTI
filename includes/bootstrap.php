<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require_once __DIR__ . '/auth.php';
