<?php
// ── ALL logic + redirects BEFORE any output ──────────────────────────────
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require __DIR__ . '/../config/db.php';

$errors   = [];
$username = '';
$email    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($username) < 3)                      $errors[] = 'Username must be at least 3 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
    if (strlen($password) < 6)                      $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)                     $errors[] = 'Passwords do not match.';

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

                $_SESSION['flash']['success'] = 'Account created! Please log in.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $ex) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
// ── HTML output starts here ───────────────────────────────────────────────
include __DIR__ . '/../includes/header.php';
?>

<div style="min-height:calc(100vh - var(--navbar-h) - 56px);display:flex;align-items:center;justify-content:center;padding:48px 16px;">
  <div class="auth-card-modern">

    <!-- Logo -->
    <div class="auth-logo">
      <div class="brand-icon">🍳</div>
      <h1>Create account</h1>
      <p>Join the RecipeApp community</p>
    </div>

    <!-- Errors -->
    <?php if ($errors): ?>
    <div class="flash-alert danger" style="margin-bottom:20px;">
      <i class="fa-solid fa-circle-xmark"></i>
      <ul style="margin:0;padding-left:16px;">
        <?php foreach ($errors as $err): ?>
          <li><?= e($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post" style="display:flex;flex-direction:column;gap:16px;">

      <div class="form-field">
        <label class="form-label-custom">Username</label>
        <input class="form-input-custom" type="text" name="username"
               value="<?= e($username) ?>" placeholder="coolchef123" required autocomplete="username">
      </div>

      <div class="form-field">
        <label class="form-label-custom">Email</label>
        <input class="form-input-custom" type="email" name="email"
               value="<?= e($email) ?>" placeholder="you@example.com" required autocomplete="email">
      </div>

      <div class="form-field">
        <label class="form-label-custom">Password</label>
        <div style="position:relative;">
          <input class="form-input-custom" type="password" id="pwd" name="password"
                 placeholder="At least 6 characters" required autocomplete="new-password" style="padding-right:44px;">
          <button type="button" onclick="togglePwd('pwd','eyePwd')"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-light);cursor:pointer;font-size:.9rem;">
            <i class="fa-regular fa-eye" id="eyePwd"></i>
          </button>
        </div>
      </div>

      <div class="form-field" style="margin-bottom:4px;">
        <label class="form-label-custom">Confirm Password</label>
        <div style="position:relative;">
          <input class="form-input-custom" type="password" id="pwd2" name="confirm_password"
                 placeholder="Repeat password" required autocomplete="new-password" style="padding-right:44px;">
          <button type="button" onclick="togglePwd('pwd2','eyePwd2')"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-light);cursor:pointer;font-size:.9rem;">
            <i class="fa-regular fa-eye" id="eyePwd2"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary-custom" style="width:100%;justify-content:center;padding:13px;">
        <i class="fa-solid fa-user-plus"></i> Create Account
      </button>

    </form>

    <p style="text-align:center;margin-top:20px;font-size:.88rem;color:var(--text-muted);">
      Already have an account?
      <a href="<?= url('auth/login.php') ?>" style="color:var(--clr-600);font-weight:700;">Log in</a>
    </p>

  </div>
</div>

<script>
function togglePwd(inputId, iconId) {
  var input = document.getElementById(inputId);
  var icon  = document.getElementById(iconId);
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
