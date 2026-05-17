<?php
// ── ALL logic + redirects BEFORE any output ──────────────────────────────
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require __DIR__ . '/../config/db.php';

$error      = '';
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password']        ?? '';

    if ($identifier === '' || $password === '') {
        $error = 'Both fields are required.';
    } else {
        $stmt = $pdo->prepare("
            SELECT id, username, email, password, role, created_at
            FROM users
            WHERE email = :email OR username = :username
            LIMIT 1
        ");
        $stmt->execute([':email' => $identifier, ':username' => $identifier]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid username/email or password.';
        } else {
            unset($user['password']);
            $_SESSION['user'] = $user;
            header('Location: ' . url('index.php'));
            exit;
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
      <h1>Welcome back</h1>
      <p>Sign in to your account</p>
    </div>

    <!-- Error -->
    <?php if ($error): ?>
    <div class="flash-alert danger" style="margin-bottom:20px;">
      <i class="fa-solid fa-circle-xmark"></i>
      <?= e($error) ?>
    </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="post" style="display:flex;flex-direction:column;gap:16px;">

      <div class="form-field">
        <label class="form-label-custom">Username or Email</label>
        <input class="form-input-custom" type="text" name="identifier"
               value="<?= e($identifier) ?>" placeholder="your@email.com"
               required autocomplete="username">
      </div>

      <div class="form-field" style="margin-bottom:4px;">
        <label class="form-label-custom">Password</label>
        <div style="position:relative;">
          <input class="form-input-custom" type="password" id="pwd" name="password"
                 placeholder="Your password" required autocomplete="current-password" style="padding-right:44px;">
          <button type="button" onclick="togglePwd()"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-light);cursor:pointer;font-size:.9rem;">
            <i class="fa-regular fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary-custom" style="width:100%;justify-content:center;padding:13px;">
        <i class="fa-solid fa-right-to-bracket"></i> Sign In
      </button>

    </form>

    <p style="text-align:center;margin-top:20px;font-size:.88rem;color:var(--text-muted);">
      No account yet?
      <a href="<?= url('auth/register.php') ?>" style="color:var(--clr-600);font-weight:700;">Create one</a>
    </p>

  </div>
</div>

<script>
function togglePwd() {
  var input = document.getElementById('pwd');
  var icon  = document.getElementById('eyeIcon');
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
