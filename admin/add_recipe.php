
<?php

// admin/recipes.php — compact with serial numbers (fixed join)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Wrapper to load main add_recipe.php for admin routing
require_once __DIR__ . '/../add_recipe.php';
