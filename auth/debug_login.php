<?php
/**
 * debug_login.php
 * 
 * Purpose: Verify that your app connects to the intended DB and that password_verify()
 * works for the admin credentials using the SAME db.php the app uses.
 * 
 * Usage (examples):
 *   http://localhost/recipe_hub_dynamic/auth/debug_login.php
 *   http://localhost/recipe_hub_dynamic/auth/debug_login.php?u=kushal.upr@gmail.com&p=Admin@123
 *   http://localhost/recipe_hub_dynamic/auth/debug_login.php?u=kushal.upr@gmail.com&p=kushal@6257
 */
header('Content-Type: text/plain');

require_once __DIR__ . '/../config/db.php';

$u = isset($_GET['u']) ? trim($_GET['u']) : 'kushal.upr@gmail.com';
$p = isset($_GET['p']) ? $_GET['p'] : 'Admin@123';

echo "Debug login check\n";
echo "User input u: {$u}\n";
echo "User input p: {$p}\n\n";

try {
    // Which database are we connected to?
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "Connected DATABASE(): {$dbName}\n";

    // Fetch matching row
    $stmt = $pdo->prepare('SELECT id, username, email, password, role FROM users WHERE username = :u OR email = :u LIMIT 1');
    $stmt->execute(['u' => $u]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "No user found for that username/email.\n";
        exit;
    }

    echo "Row found: id={$user['id']} username={$user['username']} email={$user['email']} role={$user['role']}\n";
    $hash = $user['password'];
    echo "Hash prefix: " . substr($hash, 0, 4) . "  length=" . strlen($hash) . "\n";

    $ok = password_verify($p, $hash);
    echo "password_verify(): " . ($ok ? "TRUE ✅" : "FALSE ❌") . "\n";

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}