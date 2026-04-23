<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php'; // if url() function is here

/* =========================
   1. Admin Access Check
========================= */
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    $_SESSION['flash']['danger'] = 'Admin access required.';
    header('Location: ' . url('index.php'));
    exit;
}

/* =========================
   2. Allow POST Only
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $_SESSION['flash']['danger'] = 'Invalid request method.';
    header('Location: ' . url('admin/recipes.php'));
    exit;
}

/* =========================
   3. CSRF Validation
========================= */
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    $_SESSION['flash']['danger'] = 'Security verification failed.';
    header('Location: ' . url('admin/recipes.php'));
    exit;
}

/* =========================
   4. Validate ID
========================= */
$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash']['danger'] = 'Invalid recipe ID.';
    header('Location: ' . url('admin/recipes.php'));
    exit;
}

/* =========================
   5. Fetch Recipe Image
========================= */
$stmt = $pdo->prepare("SELECT image FROM recipes WHERE id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recipe) {
    $_SESSION['flash']['danger'] = 'Recipe not found.';
    header('Location: ' . url('admin/recipes.php'));
    exit;
}

/* =========================
   6. Delete Recipe
========================= */
$pdo->prepare("DELETE FROM recipes WHERE id = ?")->execute([$id]);

/* =========================
   7. Delete Image (if exists)
========================= */
if (!empty($recipe['image'])) {
    $uploadDir = realpath(__DIR__ . '/../uploads');
    $imagePath = $uploadDir . '/' . basename($recipe['image']);

    if ($uploadDir && file_exists($imagePath)) {
        unlink($imagePath);
    }
}

/* =========================
   8. Success Redirect
========================= */
$_SESSION['flash']['success'] = 'Recipe deleted successfully.';
header('Location: ' . url('admin/recipes.php'));
exit;