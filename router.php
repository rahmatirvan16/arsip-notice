<?php
// Router for PHP built-in server to work with Slim
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['SCRIPT_NAME'] = '/router.php';
$_SERVER['PHP_SELF'] = '/router.php';

// Check if request is for API routes
if (preg_match('#^/api/(pdfs|pdf/.*)$#', $_SERVER['REQUEST_URI'])) {
    require 'api.php';
} else {
    // For non-API routes, redirect to login if not logged in
    session_start();
    if (!isset($_SESSION['user_id'])) {
        require 'login.php';
    } else {
        require 'index.php';
    }
}
