<?php
require __DIR__ . '/../config/db.php'; // gives $pdo, e(), url(), session

$error = '';
$identifier = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identifier = trim($_POST["identifier"] ?? '');
    $password   = $_POST["password"] ?? '';

    if ($identifier === '' || $password === '') {
        $error = "All fields are required.";
    } else {
        // Look up by email OR username
        $stmt = $pdo->prepare("
            SELECT id, username, email, password, role, created_at
            FROM users
            WHERE email = :email OR username = :username
            LIMIT 1
        ");
        $stmt->execute([
            ':email'    => $identifier,
            ':username' => $identifier,
        ]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user["password"])) {
            $error = "Invalid email/username or password.";
        } else {
            // Login success: store user (without password)
            unset($user['password']);
            $_SESSION['user'] = $user;

            header('Location: ' . url('/'));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log in - Recipe App</title>
    <link rel="stylesheet" href="<?php echo url('/css/style.css'); ?>">

    <!-- Page-specific styles: card container + button -->
    <style>
        .auth-page {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: calc(100vh - 80px); /* header & footer space */
            padding: 40px 16px;
        }

        .auth-card {
            width: 380px;
            background: #ffffff;
            padding: 32px 28px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .auth-card h1 {
            margin-bottom: 20px;
            font-size: 28px;
        }

        .auth-card .form-group {
            margin-bottom: 16px;
        }

        .auth-card label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .auth-card input[type="text"],
        .auth-card input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            font-size: 15px;
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .btn {
            display: inline-block;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.1s ease;
        }

        .btn-primary {
            background: #1a73e8;   /* blue like Google / your other site */
            color: #ffffff;
        }

        .auth-btn {
            width: 100%;
            padding: 10px 0;
            margin-top: 8px;
            border-radius: 6px;
            font-size: 16px;
        }

        .btn-primary:hover {
            background: #1664cc;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            background: #1357b3;
            transform: translateY(0);
        }

        .auth-footer {
            margin-top: 16px;
            font-size: 14px;
            text-align: center;
        }

        .alert.alert-danger {
            background: #fdecea;
            color: #b71c1c;
            padding: 10px 12px;
            border-radius: 4px;
            margin-bottom: 16px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="auth-page">
        <div class="auth-card">
            <h1>Log in</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="identifier">Username or Email</label>
                    <input
                        type="text"
                        id="identifier"
                        name="identifier"
                        value="<?php echo e($identifier); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary auth-btn">Login</button>
            </form>

            <p class="auth-footer">
                No account? <a href="<?php echo url('/auth/register.php'); ?>">Create one</a>
            </p>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
