<?php
$__ROOT_BASE = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'); /*ROOT_BASE*/

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($username === '' || strlen($username) < 3) $errors[] = 'Username must be at least 3 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e");
            $stmt->execute([':u'=>$username, ':e'=>$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Username or email already taken.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:u,:e,:p)");
                $stmt->execute([':u'=>$username, ':e'=>$email, ':p'=>$hash]);
                $_SESSION['flash']['success'] = 'Account created! You can log in now.';
                header('Location: ' . $__ROOT_BASE . '/auth/login.php');
exit;
            }
        } catch (Throwable $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h3 mb-3">Create your account</h1>
        <?php
$__ROOT_BASE = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'); /*ROOT_BASE*/
 if ($errors): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php
$__ROOT_BASE = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'); /*ROOT_BASE*/
 foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php
$__ROOT_BASE = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'); /*ROOT_BASE*/
 endforeach; ?>
            </ul>
          </div>
        <?php
$__ROOT_BASE = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'); /*ROOT_BASE*/
 endif; ?>
        <form method="post" novalidate>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-4">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
          </div>
          <div class="d-grid">
            <button class="btn btn-primary" type="submit">Create Account</button>
          </div>
          <p class="mt-3 mb-0 text-center">Already have an account? <a href="/auth/login.php">Log in</a></p>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
$__ROOT_BASE = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/'); /*ROOT_BASE*/
 include __DIR__ . '/../includes/footer.php'; ?>
