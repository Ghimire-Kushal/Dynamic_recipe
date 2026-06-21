<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require __DIR__ . '/../config/db.php';

if (is_logged_in()) { header('Location: ' . url('index.php')); exit; }

$token  = trim($_GET['token'] ?? '');
$errors = [];
$done   = false;

// Validate token on every load
if ($token === '') {
    $tokenError = 'No reset token provided.';
} else {
    $row = $pdo->prepare("
        SELECT password_resets.*, users.email
        FROM   password_resets
        JOIN   users ON users.id = password_resets.user_id
        WHERE  password_resets.token = ?
          AND  password_resets.used = 0
        LIMIT  1
    ");
    $row->execute([$token]);
    $row = $row->fetch();

    if (!$row) {
        $tokenError = 'This reset link is invalid or has already been used.';
    } elseif (strtotime($row['expires_at']) < time()) {
        $tokenError = 'This reset link has expired. Please request a new one.';
    }
}

// Handle form submission
if (!isset($tokenError) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6)
        $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)
        $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Update password and mark token used in one transaction
        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
                ->execute([$hash, $row['user_id']]);
            $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?")
                ->execute([$token]);
            $pdo->commit();
            $done = true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

$pageTitle = 'Set New Password — RecipeApp';
include __DIR__ . '/../includes/header.php';
?>

<div class="auth-page-wrap">

  <div class="auth-card animate-card">

    <?php if (isset($tokenError)): ?>

      <!-- ── Invalid / Expired token ── -->
      <div class="auth-header">
        <div class="auth-icon-ring auth-icon-ring--error">
          <i class="fa-solid fa-link-slash"></i>
        </div>
        <h1 class="auth-title">Link invalid</h1>
        <p class="auth-sub"><?= e($tokenError) ?></p>
      </div>

      <a href="<?= url('auth/forgot-password.php') ?>" class="auth-btn" style="display:flex;margin-top:8px;">
        <i class="fa-solid fa-paper-plane"></i> Request a new link
      </a>

    <?php elseif ($done): ?>

      <!-- ── Success ── -->
      <div class="auth-header">
        <div class="auth-icon-ring auth-icon-ring--success">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1 class="auth-title">Password updated!</h1>
        <p class="auth-sub">You can now sign in with your new password.</p>
      </div>

      <?php
        $_SESSION['flash']['success'] = 'Password changed successfully. Welcome back!';
      ?>

      <a href="<?= url('auth/login.php') ?>" class="auth-btn" style="display:flex;margin-top:8px;">
        <i class="fa-solid fa-right-to-bracket"></i> Go to Login
      </a>

    <?php else: ?>

      <!-- ── Form ── -->
      <div class="auth-header">
        <div class="auth-icon-ring">
          <i class="fa-solid fa-lock-open"></i>
        </div>
        <h1 class="auth-title">New password</h1>
        <p class="auth-sub">
          Setting password for
          <strong style="color:var(--text-main)"><?= e($row['email']) ?></strong>
        </p>
      </div>

      <?php if ($errors): ?>
        <div class="auth-alert auth-alert--error">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <form method="post" class="auth-form" novalidate>
        <input type="hidden" name="token" value="<?= e($token) ?>">

        <div class="auth-field">
          <label for="pwd" class="auth-label">
            <i class="fa-solid fa-key"></i> New password
          </label>
          <div class="auth-input-wrap">
            <input
              id="pwd" name="password" type="password"
              class="auth-input"
              placeholder="At least 6 characters"
              required autocomplete="new-password" autofocus
              oninput="checkStrength(this.value)">
            <button type="button" class="auth-eye-btn" onclick="togglePwd('pwd','eye1')" aria-label="Show">
              <i class="fa-regular fa-eye" id="eye1"></i>
            </button>
          </div>
          <div class="pwd-strength-bar">
            <div class="pwd-strength-fill" id="strengthFill"></div>
          </div>
          <span class="pwd-strength-label" id="strengthLabel"></span>
        </div>

        <div class="auth-field">
          <label for="pwd2" class="auth-label">
            <i class="fa-solid fa-shield-halved"></i> Confirm password
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

        <!-- Token also in URL for GET, but submit via POST -->
        <button type="submit" class="auth-btn">
          <i class="fa-solid fa-floppy-disk"></i> Save New Password
        </button>

      </form>

    <?php endif; ?>

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
  if (val.length >= 6)             score++;
  if (val.length >= 10)            score++;
  if (/[A-Z]/.test(val))          score++;
  if (/[0-9]/.test(val))          score++;
  if (/[^A-Za-z0-9]/.test(val))   score++;
  var levels = [
    { pct:'0%',   color:'transparent',    text:'' },
    { pct:'25%',  color:'#ef4444',        text:'Weak' },
    { pct:'50%',  color:'#f97316',        text:'Fair' },
    { pct:'75%',  color:'#eab308',        text:'Good' },
    { pct:'100%', color:'var(--clr-500)', text:'Strong' },
  ];
  var l = val.length === 0 ? levels[0] : levels[Math.min(score, 4)];
  fill.style.width      = l.pct;
  fill.style.background = l.color;
  label.textContent     = l.text;
  label.style.color     = l.color;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
