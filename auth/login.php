<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require __DIR__ . '/../config/db.php';

// Already logged in
if (is_logged_in()) { header('Location: ' . url('index.php')); exit; }

$error      = '';
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password']        ?? '';

    if ($identifier === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("
            SELECT id, username, email, password, role
            FROM   users
            WHERE  email = :id OR username = :id2
            LIMIT  1
        ");
        $stmt->execute([':id' => $identifier, ':id2' => $identifier]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid username/email or password.';
        } else {
            unset($user['password']);
            $_SESSION['user'] = $user;
            $redirect = $_SESSION['intended_url'] ?? url('index.php');
            unset($_SESSION['intended_url']);
            header('Location: ' . $redirect);
            exit;
        }
    }
}

$pageTitle = 'Sign In — RecipeApp';
include __DIR__ . '/../includes/header.php';
?>

<div class="auth-page-wrap">

  <div class="auth-card animate-card">

    <!-- Header -->
    <div class="auth-header">
      <div class="auth-icon-ring">
        <i class="fa-solid fa-lock"></i>
      </div>
      <h1 class="auth-title">Welcome back</h1>
      <p class="auth-sub">Sign in to your RecipeApp account</p>
    </div>

    <!-- Success flash (from register or reset) -->
    <?php if (!empty($_SESSION['flash']['success'])): ?>
      <div class="auth-alert auth-alert--success">
        <i class="fa-solid fa-circle-check"></i>
        <?= e($_SESSION['flash']['success']) ?>
      </div>
      <?php unset($_SESSION['flash']['success']); ?>
    <?php endif; ?>

    <!-- Error -->
    <?php if ($error): ?>
      <div class="auth-alert auth-alert--error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <?= e($error) ?>
      </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post" class="auth-form" novalidate>

      <div class="auth-field">
        <label for="identifier" class="auth-label">
          <i class="fa-regular fa-user"></i> Username or Email
        </label>
        <input
          id="identifier" name="identifier" type="text"
          class="auth-input<?= $error ? ' auth-input--error' : '' ?>"
          value="<?= e($identifier) ?>"
          placeholder="your@email.com or username"
          required autocomplete="username" autofocus>
      </div>

      <div class="auth-field">
        <div class="auth-label-row">
          <label for="pwd" class="auth-label">
            <i class="fa-solid fa-key"></i> Password
          </label>
          <a href="<?= url('auth/forgot-password.php') ?>" class="auth-link-sm">
            Forgot password?
          </a>
        </div>
        <div class="auth-input-wrap">
          <input
            id="pwd" name="password" type="password"
            class="auth-input<?= $error ? ' auth-input--error' : '' ?>"
            placeholder="••••••••"
            required autocomplete="current-password">
          <button type="button" class="auth-eye-btn" onclick="togglePwd('pwd','eyeIcon')" aria-label="Show password">
            <i class="fa-regular fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="auth-btn">
        <i class="fa-solid fa-right-to-bracket"></i>
        Sign In
      </button>

    </form>

    <p class="auth-footer-text">
      No account yet?
      <a href="<?= url('auth/register.php') ?>" class="auth-link">Create one</a>
    </p>

  </div>

</div>

<script>
function togglePwd(inputId, iconId) {
  var el   = document.getElementById(inputId);
  var icon = document.getElementById(iconId);
  var show = el.type === 'password';
  el.type  = show ? 'text' : 'password';
  icon.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
