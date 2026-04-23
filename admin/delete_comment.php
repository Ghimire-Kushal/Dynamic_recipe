
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/db.php';
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    $_SESSION['flash']['danger'] = 'Admin access required.';
    header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/index.php'); exit;
}
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
function csrf_field(){ echo '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">'; }
function check_csrf(){ if(($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')){ http_response_code(403); exit('Invalid CSRF token'); } }

if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit('Method not allowed'); }
check_csrf();
$id = (int)($_POST['id'] ?? 0);
$pdo->prepare("DELETE FROM comments WHERE id=?")->execute([$id]);
$_SESSION['flash']['success']='Comment deleted.';
header('Location: ' . url('admin/comments.php')); exit;
