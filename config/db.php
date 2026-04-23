<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Start Session (safe)
|--------------------------------------------------------------------------
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Error Reporting (development only)
|--------------------------------------------------------------------------
*/
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| Database Configuration (XAMPP Localhost)
|--------------------------------------------------------------------------
*/
$DB_HOST = 'localhost';
$DB_PORT = '3306';
$DB_NAME = 'dynamic_recipe';
$DB_USER = 'root';
$DB_PASS = ''; 

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    exit('❌ Database connection failed.');
}

/*
|--------------------------------------------------------------------------
| Base URL (AUTO detect for localhost)
|--------------------------------------------------------------------------
*/
if (!defined('BASE_PATH')) {
    $projectFolder = 'dynamic_recipe'; // 🔥 CHANGE if your folder name differs
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_PATH', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/' . $projectFolder);
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