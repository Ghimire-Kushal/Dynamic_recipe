<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| Load .env file (never committed to git)
|--------------------------------------------------------------------------
*/
$_envFile = dirname(__DIR__) . '/.env';
if (file_exists($_envFile)) {
    foreach (file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_line) {
        $_line = trim($_line);
        if ($_line === '' || $_line[0] === '#') continue;
        $parts = explode('=', $_line, 2);
        if (count($parts) === 2) {
            [$_key, $_val] = $parts;
            $_key = trim($_key);
            $_val = trim($_val);
            if (!array_key_exists($_key, $_ENV)) {
                putenv("$_key=$_val");
                $_ENV[$_key] = $_val;
            }
        }
    }
}
unset($_envFile, $_line, $parts, $_key, $_val);

/*
|--------------------------------------------------------------------------
| Database connection — values come from .env, never from this file
|--------------------------------------------------------------------------
*/
$_host = getenv('DB_HOST') ?: 'localhost';
$_port = getenv('DB_PORT') ?: '3306';
$_name = getenv('DB_NAME') ?: 'dynamic_recipe';
$_user = getenv('DB_USER') ?: 'root';
$_pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$_host};port={$_port};dbname={$_name};charset=utf8mb4",
        $_user,
        $_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    exit('Database connection failed. Check your .env file.');
}
unset($_host, $_port, $_name, $_user, $_pass);

/*
|--------------------------------------------------------------------------
| Base URL
|--------------------------------------------------------------------------
*/
if (!defined('BASE_PATH')) {
    $protocol    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host        = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $pathParts   = array_values(array_filter(explode('/', $_SERVER['SCRIPT_NAME'] ?? '')));
    $projectFolder = $pathParts[0] ?? basename(dirname(__DIR__));
    define('BASE_PATH', $protocol . '://' . $host . '/' . $projectFolder);
}

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/
function url(string $path = ''): string {
    return rtrim(BASE_PATH, '/') . '/' . ltrim($path, '/');
}

function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

function is_admin(): bool {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
