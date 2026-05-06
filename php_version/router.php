<?php
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$fullPath = __DIR__ . '/' . ltrim($path, '/');

// If the requested file or directory exists, let the built-in server handle it
if (is_file($fullPath) || is_dir($fullPath)) {
    return false;
}

// Otherwise, serve index.php
require_once 'index.php';
?>