<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require __DIR__ . '/../config/db.php';

if (is_logged_in()) { header('Location: ' . url('index.php')); exit; }

$sent    = false;
$error   = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } else {
        $user = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
        $user->execute([$email]);
        $user = $user->fetch();

        if ($user) {
            // Delete any previous unused tokens for this user
            $pdo->prepare("DELETE FROM password_resets WHERE user_id = ? AND used = 0")
                ->execute([$user['id']]);

            // Generate a cryptographically secure 64-char hex token
            $token     = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)")
                ->execute([$user['id'], $token, $expiresAt]);

            $resetLink = url('auth/reset-password.php') . '?token=' . urlencode($token);

            /*
             * In production: send $resetLink via email (PHPMailer / sendmail / SMTP).
             * For local development we display it directly on the page.
             */
        }

        // Always show "sent" — prevents email enumeration
        $sent = true;
    }
}

$pageTitle = 'Forgot Password — RecipeApp';
include __DIR__ . '/../includes/header.php';
?>

<div class="auth-page-wrap">

  <div class="auth-card animate-card">

    <!-- Header -->
    <div class="auth-header">
      <div class="auth-icon-ring">
        <i class="fa-solid fa-envelope-open-text"></i>
      </div>
      <h1 class="auth-title">Reset password</h1>
      <p class="auth-sub">We'll generate a secure reset link for you</p>
    </div>

    <?php if ($sent): ?>

      <!-- ── Sent state ── -->
      <div class="auth-alert auth-alert--success">
        <i class="fa-solid fa-circle-check"></i>
        If an account with that email exists, a reset link has been generated.
      </div>

      <?php if (isset($resetLink)): ?>
      <!-- DEV MODE: show link — remove this block in production -->
      <div class="auth-dev-box">
        <div class="auth-dev-badge">
          <i class="fa-solid fa-code"></i> Dev mode — reset link:
        </div>
        <a href="<?= e($resetLink) ?>" class="auth-dev-link"><?= e($resetLink) ?></a>
        <p class="auth-dev-note">In production this would be emailed. Link expires in 1 hour.</p>
      </div>
      <?php endif; ?>

      <div class="auth-btn-group">
        <a href="<?= url('auth/forgot-password.php') ?>" class="auth-btn auth-btn--outline">
          <i class="fa-solid fa-rotate-left"></i> Send another
        </a>
        <a href="<?= url('auth/login.php') ?>" class="auth-btn">
          <i class="fa-solid fa-right-to-bracket"></i> Back to Login
        </a>
      </div>

    <?php else: ?>

      <!-- ── Form state ── -->
      <?php if ($error): ?>
        <div class="auth-alert auth-alert--error">
          <i class="fa-solid fa-triangle-exclamation"></i> <?= e($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="auth-form" novalidate>

        <div class="auth-field">
          <label for="email" class="auth-label">
            <i class="fa-regular fa-envelope"></i> Email address
          </label>
          <input
            id="email" name="email" type="email"
            class="auth-input<?= $error ? ' auth-input--error' : '' ?>"
            value="<?= e($email) ?>"
            placeholder="you@example.com"
            required autocomplete="email" autofocus>
          <span class="auth-hint">We'll send a reset link to this address</span>
        </div>

        <button type="submit" class="auth-btn">
          <i class="fa-solid fa-paper-plane"></i>
          Send Reset Link
        </button>

      </form>

      <p class="auth-footer-text">
        Remembered it?
        <a href="<?= url('auth/login.php') ?>" class="auth-link">Sign in</a>
      </p>

    <?php endif; ?>

  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
