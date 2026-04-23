<?php
/**
 * force_admin_and_test.php
 * See previous message for description.
 */
header('Content-Type: text/plain');
require_once __DIR__ . '/../config/db.php';
$username = 'Kushal Ghimire';
$email    = 'kushal.upr@gmail.com';
$plain    = 'kushal@6257';
$hash     = password_hash($plain, PASSWORD_DEFAULT);
try {
    $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "Connected DATABASE(): {$db}\n";
    $pdo->exec("ALTER TABLE users MODIFY role ENUM('user','admin') NOT NULL DEFAULT 'user'");
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $pdo->prepare('UPDATE users SET username=:u, password=:p, role="admin" WHERE id=:id')
            ->execute(['u'=>$username, 'p'=>$hash, 'id'=>$row['id']]);
        $id = $row['id'];
        echo "Updated existing admin id={$id}\n";
    } else {
        $pdo->prepare('INSERT INTO users (username,email,password,role) VALUES (:u,:e,:p,"admin")')
            ->execute(['u'=>$username,'e'=>$email,'p'=>$hash]);
        $id = $pdo->lastInsertId();
        echo "Inserted new admin id={$id}\n";
    }
    $u = $pdo->prepare('SELECT id,username,email,password,role FROM users WHERE email=:e LIMIT 1');
    $u->execute(['e'=>$email]);
    $user = $u->fetch(PDO::FETCH_ASSOC);
    if (!$user) { echo "ERROR: admin row not found after upsert.\n"; exit; }
    echo "Row: id={$user['id']} username={$user['username']} email={$user['email']} role={$user['role']}\n";
    echo "Hash prefix=" . substr($user['password'],0,4) . " length=" . strlen($user['password']) . "\n";
    $ok = password_verify($plain, $user['password']);
    echo "password_verify(entered): " . ($ok ? "TRUE ✅" : "FALSE ❌") . "\n";
    echo "\nNow try logging in with:\n  Email: {$email}\n  Password: {$plain}\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
