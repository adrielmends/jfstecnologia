<?php
/**
 * Router script for PHP built-in server (php -S)
 * Emulates .htaccess rules for local development
 */

$uri = decodeURI($_SERVER['REQUEST_URI']);
$path = parse_url($uri, PHP_URL_PATH);
$file = __DIR__ . $path;

// 1. Serve static files if they exist directly
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// 2. Specific Rewrites from .htaccess
$rewrites = [
    '/admin'       => 'admin.php',
    '/admin-login' => 'admin-auth.php',
    '/painel'      => 'painel.php',
    '/login'       => 'login.html',
    '/logout'      => 'logout.php'
];

foreach ($rewrites as $pattern => $target) {
    if (preg_match("#^" . $pattern . "/?$#i", $path)) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . $target;
        return;
    }
}

// 3. Generic Clean URLs for .php
if (file_exists(__DIR__ . $path . '.php')) {
    require_once __DIR__ . $path . '.php';
    return;
}

// 4. Generic Clean URLs for .html
if (file_exists(__DIR__ . $path . '.html')) {
    include __DIR__ . $path . '.html';
    return;
}

// 5. Fallback to index.html (Main landing) or index.php (WP)
if ($path === '/' || $path === '') {
    if (file_exists(__DIR__ . '/index.html')) {
        include __DIR__ . '/index.html';
        return;
    }
}

if (file_exists(__DIR__ . '/index.php')) {
    require_once __DIR__ . '/index.php';
    return;
}

// Default 404
return false;

function decodeURI($uri) {
    return rawurldecode($uri);
}
