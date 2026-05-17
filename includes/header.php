<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!function_exists('url')) {
  function url(string $path=''): string {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $base . '/' . ltrim($path,'/');
  }
}
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

require_once __DIR__ . '/../config/db.php';

$categories = [];
try {
  $stmt = $pdo->query("SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL AND category <> '' ORDER BY category");
  $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {}

if (!function_exists('is_logged_in')) {
  function is_logged_in(): bool { return !empty($_SESSION['user']); }
}
if (!function_exists('current_user_id')) {
  function current_user_id() { return $_SESSION['user']['id'] ?? null; }
}

$currentUser = $_SESSION['user'] ?? null;

// detect active page for nav highlights
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recipe App</title>

  <!-- Preconnect for Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Bootstrap 5 (grid + utilities only; visual overrides in style.css) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Our design system -->
  <link href="<?= url('public/style.css') ?>" rel="stylesheet">

  <!-- Inline: apply saved theme/settings before first paint to prevent flash -->
  <script>
    (function(){
      var s = {};
      try { s = JSON.parse(localStorage.getItem('rcpSettings') || '{}'); } catch(e){}
      var b = document.documentElement;
      // Apply to <html> early so body inherits before it renders
      if(s.theme)      b.setAttribute('data-theme',      s.theme);
      if(s.color)      b.setAttribute('data-color',      s.color);
      if(s.card)       b.setAttribute('data-card',       s.card);
      if(s.navbar)     b.setAttribute('data-navbar',     s.navbar);
      if(s.layout)     b.setAttribute('data-layout',     s.layout);
      if(s.font)       b.setAttribute('data-font',       s.font);
      if(s.animations) b.setAttribute('data-animations', s.animations);
      // also apply hue CSS var immediately
      var hues={indigo:239,green:142,orange:25,red:0,purple:270,teal:175};
      var sats={indigo:'80%',green:'65%',orange:'95%',red:'78%',purple:'75%',teal:'70%'};
      var c = s.color || 'indigo';
      if(hues[c]) {
        b.style.setProperty('--hue', hues[c]);
        b.style.setProperty('--sat', sats[c]);
      }
    })();
  </script>
</head>
<body>

<nav class="site-nav navbar navbar-expand-lg">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand" href="<?= url('index.php') ?>">
      <span class="brand-icon">🍳</span>
      <span>RecipeApp</span>
    </a>

    <!-- Mobile toggler -->
    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navContent"
            aria-controls="navContent" aria-expanded="false">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navContent">

      <!-- Left nav links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='index.php'?'active':'' ?>"
             href="<?= url('index.php') ?>">
            <i class="fa-solid fa-house me-1" style="font-size:.78rem;"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='recipes.php'?'active':'' ?>"
             href="<?= url('recipes.php') ?>">
            <i class="fa-solid fa-bowl-food me-1" style="font-size:.78rem;"></i>Recipes
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='add_recipe.php'?'active':'' ?>"
             href="<?= url('add_recipe.php') ?>">
            <i class="fa-solid fa-plus me-1" style="font-size:.78rem;"></i>Add Recipe
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='about.php'?'active':'' ?>"
             href="<?= url('about.php') ?>">
            <i class="fa-solid fa-circle-info me-1" style="font-size:.78rem;"></i>About
          </a>
        </li>
        <?php if ($currentUser && ($currentUser['role'] ?? '') === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?= url('admin/index.php') ?>">
            <i class="fa-solid fa-gauge me-1" style="font-size:.78rem;"></i>Admin
          </a>
        </li>
        <?php endif; ?>
      </ul>

      <!-- Search bar (desktop) -->
      <form class="d-none d-lg-flex me-3" method="get" action="<?= url('index.php') ?>">
        <div class="nav-search-wrap">
          <input type="search" name="q" placeholder="Search recipes…"
                 value="<?= e($_GET['q'] ?? '') ?>">
          <select name="category">
            <option value="">All</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= e($cat) ?>" <?= (($_GET['category'] ?? '') === $cat)?'selected':'' ?>>
                <?= e(ucfirst($cat)) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
      </form>

      <!-- Auth buttons -->
      <ul class="navbar-nav ms-0 gap-2 align-items-center">
        <?php if ($currentUser): ?>
          <li class="nav-item">
            <span class="nav-link d-flex align-items-center gap-2" style="font-size:.85rem;font-weight:600;">
              <span style="width:28px;height:28px;background:var(--hero-grad);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:white;font-size:.75rem;font-weight:800;">
                <?= strtoupper(substr($currentUser['username'],0,1)) ?>
              </span>
              <?= e($currentUser['username']) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="btn-nav-login nav-link" href="<?= url('auth/logout.php') ?>">
              <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn-nav-login nav-link" href="<?= url('auth/login.php') ?>">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn-nav-register nav-link" href="<?= url('auth/register.php') ?>">
              Get Started
            </a>
          </li>
        <?php endif; ?>
      </ul>

    </div>
  </div>
</nav>

<!-- Mobile search (shows below navbar on small screens) -->
<form class="d-lg-none px-3 py-2 border-bottom" style="background:var(--bg-nav);backdrop-filter:blur(16px);"
      method="get" action="<?= url('index.php') ?>">
  <div class="nav-search-wrap" style="max-width:100%;">
    <input type="search" name="q" placeholder="Search recipes…"
           value="<?= e($_GET['q'] ?? '') ?>" style="width:100%;">
    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
  </div>
</form>

<main class="main-content">
<?php
// Flash messages
if (!empty($_SESSION['flash'])):
  foreach ($_SESSION['flash'] as $type => $msg):
    $icon = $type === 'success' ? 'fa-circle-check' : ($type === 'danger' ? 'fa-circle-xmark' : 'fa-circle-info');
?>
  <div class="container pt-4">
    <div class="flash-alert <?= e($type) ?>">
      <i class="fa-solid <?= $icon ?>"></i>
      <?= e($msg) ?>
    </div>
  </div>
<?php
  endforeach;
  unset($_SESSION['flash']);
endif;
?>
