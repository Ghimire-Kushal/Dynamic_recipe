<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require __DIR__ . '/../config/db.php';

if (is_logged_in()) { header('Location: ' . url('index.php')); exit; }

$errors   = [];
$username = '';
$email    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validation
    if (strlen($username) < 3)
        $errors[] = 'Username must be at least 3 characters.';
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username))
        $errors[] = 'Username may only contain letters, numbers, and underscores.';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Enter a valid email address.';

    if (strlen($password) < 6)
        $errors[] = 'Password must be at least 6 characters.';

    if ($password !== $confirm)
        $errors[] = 'Passwords do not match.';

    if (!$errors) {
        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $check->execute([$username, $email]);
            if ($check->fetch()) {
                $errors[] = 'That username or email is already taken.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')")
                    ->execute([$username, $email, $hash]);

                $_SESSION['flash']['success'] = 'Account created! You can now sign in.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $ex) {
            $errors[] = 'Registration failed — please try again.';
        }
    }
}

$pageTitle = 'Create Account — RecipeApp';
include __DIR__ . '/../includes/header.php';
?>

<div class="auth-page-wrap">

  <div class="auth-card animate-card">

    <!-- Header -->
    <div class="auth-header">
      <div class="auth-icon-ring">
        <i class="fa-solid fa-user-plus"></i>
      </div>
      <h1 class="auth-title">Create account</h1>
      <p class="auth-sub">Join the RecipeApp community</p>
    </div>

    <!-- Errors -->
    <?php if ($errors): ?>
      <div class="auth-alert auth-alert--error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post" class="auth-form" novalidate>

      <div class="auth-field">
        <label for="username" class="auth-label">
          <i class="fa-regular fa-user"></i> Username
        </label>
        <input
          id="username" name="username" type="text"
          class="auth-input"
          value="<?= e($username) ?>"
          placeholder="coolchef123"
          required autocomplete="username" autofocus>
        <span class="auth-hint">Letters, numbers, underscores — min. 3 chars</span>
      </div>

      <div class="auth-field">
        <label for="email" class="auth-label">
          <i class="fa-regular fa-envelope"></i> Email
        </label>
        <input
          id="email" name="email" type="email"
          class="auth-input"
          value="<?= e($email) ?>"
          placeholder="you@example.com"
          required autocomplete="email">
      </div>

      <div class="auth-field">
        <label for="pwd" class="auth-label">
          <i class="fa-solid fa-key"></i> Password
        </label>
        <div class="auth-input-wrap">
          <input
            id="pwd" name="password" type="password"
            class="auth-input"
            placeholder="At least 6 characters"
            required autocomplete="new-password"
            oninput="checkStrength(this.value)">
          <button type="button" class="auth-eye-btn" onclick="togglePwd('pwd','eye1')" aria-label="Show">
            <i class="fa-regular fa-eye" id="eye1"></i>
          </button>
        </div>
        <!-- Password strength bar -->
        <div class="pwd-strength-bar">
          <div class="pwd-strength-fill" id="strengthFill"></div>
        </div>
        <span class="pwd-strength-label" id="strengthLabel"></span>
      </div>

      <div class="auth-field">
        <label for="pwd2" class="auth-label">
          <i class="fa-solid fa-shield-halved"></i> Confirm Password
        </label>
        <div class="auth-input-wrap">
          <input
            id="pwd2" name="confirm_password" type="password"
            class="auth-input"
            placeholder="Repeat password"
            required autocomplete="new-password">
          <button type="button" class="auth-eye-btn" onclick="togglePwd('pwd2','eye2')" aria-label="Show">
            <i class="fa-regular fa-eye" id="eye2"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="auth-btn">
        <i class="fa-solid fa-user-plus"></i>
        Create Account
      </button>

    </form>

    <p class="auth-footer-text">
      Already have an account?
      <a href="<?= url('auth/login.php') ?>" class="auth-link">Sign in</a>
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

function checkStrength(val) {
  var fill  = document.getElementById('strengthFill');
  var label = document.getElementById('strengthLabel');
  if (!fill) return;

  var score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  var levels = [
    { pct: '0%',   color: 'transparent', text: '' },
    { pct: '25%',  color: '#ef4444',     text: 'Weak' },
    { pct: '50%',  color: '#f97316',     text: 'Fair' },
    { pct: '75%',  color: '#eab308',     text: 'Good' },
    { pct: '100%', color: 'var(--clr-500)', text: 'Strong' },
  ];
  var level = levels[Math.min(score, 4)];
  if (val.length === 0) level = levels[0];

  fill.style.width       = level.pct;
  fill.style.background  = level.color;
  label.textContent      = level.text;
  label.style.color      = level.color;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
