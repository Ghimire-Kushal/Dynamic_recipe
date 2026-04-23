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
$st = $pdo->prepare("SELECT image FROM recipes WHERE id=?"); $st->execute([$id]); $r = $st->fetch(PDO::FETCH_ASSOC);
if($r){
  $pdo->prepare("DELETE FROM recipes WHERE id=?")->execute([$id]);
  $dir = __DIR__ . '/../uploads';
  if(!empty($r['image']) && file_exists($dir.'/'.$r['image'])) @unlink($dir.'/'.$r['image']);
  $_SESSION['flash']['success']='Recipe deleted.';
}
header('Location: ' . url('admin/recipes.php'));
exit;
