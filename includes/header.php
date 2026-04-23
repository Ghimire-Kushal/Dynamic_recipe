<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!function_exists('url')) { function url(string $path=''): string { $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); return $base . '/' . ltrim($path,'/'); } }

require_once __DIR__ . '/../config/db.php'; // expects $pdo

// Fetch categories for the navbar filter (ignore errors if table missing)
$categories = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL AND category <> '' ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) { /* ignore */}
if (!function_exists('is_logged_in')) {
  function is_logged_in(): bool { return !empty($_SESSION['user']); }
}
if (!function_exists('current_user_id')) {
  function current_user_id() { return $_SESSION['user']['id'] ?? null; }
}


$currentUser = $_SESSION['user'] ?? null;
?>
<?php $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recipe App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= url('public/style.css') ?>" rel="stylesheet">
  </head>
  <body class="bg-light" data-theme="light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
      <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('index.php') ?>">🍳 Recipe App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="<?= url('index.php') ?>">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= url('recipes.php') ?>">Recipes</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= url('add_recipe.php') ?>">Add Recipe</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= url('about.php') ?>">About</a></li>
            <?php if ($currentUser && ($currentUser['role'] ?? '') === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= url('admin/index.php') ?>">Admin</a></li>
            <?php endif; ?>
          </ul>
          <form class="d-flex" role="search" method="get" action="<?= url('index.php') ?>">
            <div class="input-group">
              <input class="form-control" type="search" name="q" placeholder="Search recipes..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
              <select class="form-select" name="category" style="max-width:12rem">
                <option value="">All</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= htmlspecialchars($cat) ?>" <?= (($_GET['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($cat)) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn btn-primary" type="submit">Search</button>
            </div>
          </form>
          <ul class="navbar-nav ms-3">
            <?php if ($currentUser): ?>
              <li class="nav-item">
                <span class="navbar-text me-2">Hi, <?= htmlspecialchars($currentUser['username']) ?></span>
              </li>
              <li class="nav-item"><a class="btn btn-outline-secondary" href="<?= url('auth/logout.php') ?>">Logout</a></li>
            <?php else: ?>
              <li class="nav-item"><a class="btn btn-outline-primary me-2" href="<?= url('auth/login.php') ?>">Login</a></li>
              <li class="nav-item"><a class="btn btn-primary" href="<?= url('auth/register.php') ?>">Register</a></li>
            <?php endif; ?>
          </ul>
          
        </div>
      </div>
    </nav>

    <main class="container py-4">
      <?php if (!empty($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $msg): ?>
          <div class="alert alert-<?= htmlspecialchars($type) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endforeach; unset($_SESSION['flash']); ?>
      <?php endif; ?>
