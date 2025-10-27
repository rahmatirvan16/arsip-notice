<?php
// Router for PHP built-in server to work with Slim
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['SCRIPT_NAME'] = '/router.php';
$_SERVER['PHP_SELF'] = '/router.php';

// Normalize path (strip query string) so routing decisions aren't broken by ?query=..
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

// Check if request is for API routes
if (preg_match('#^/api/(pdfs|pdf/.*|search)$#', $path)) {
    require 'api.php';
} elseif ($path === '/api_client.php' || $path === '/api_client') {
    // Allow direct access to api_client.php without login
    require 'api_client.php';
} else {
    // For non-API routes, redirect to login if not logged in
    // Start session only if not already started to avoid warnings
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        require 'login.php';
    } else {
        require 'index.php';
    }
}
