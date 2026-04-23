<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Database Configuration (XAMPP - Localhost)
|--------------------------------------------------------------------------
*/
$DB_HOST = 'localhost';   // ✅ use localhost for XAMPP
$DB_PORT = '3306';
$DB_NAME = 'recipe_app';
$DB_USER = 'root';
$DB_PASS = '';            // default XAMPP password is empty

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/*
|--------------------------------------------------------------------------
| Base URL (LOCAL ONLY)
|--------------------------------------------------------------------------
*/
if (!defined('BASE_PATH')) {
    define('BASE_PATH', 'http://localhost/dynamic_recipe');
}

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/
function url($path = '') {
    return rtrim(BASE_PATH, '/') . '/' . ltrim($path, '/');
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function is_admin() {
    return isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
