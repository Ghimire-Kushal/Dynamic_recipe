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

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT c.*, u.username, r.title AS recipe_title FROM comments c LEFT JOIN users u ON u.id=c.user_id LEFT JOIN recipes r ON r.id=c.recipe_id WHERE c.id=?");
$st->execute([$id]);
$c = $st->fetch(PDO::FETCH_ASSOC);
if(!$c){ $_SESSION['flash']['danger']='Comment not found.'; header('Location: '.url('admin/comments.php')); exit; }

$err = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
  check_csrf();
  $comment = trim($_POST['comment'] ?? '');
  if($comment==='') $err[]='Comment cannot be empty.';
  if(!$err){
    $u = $pdo->prepare("UPDATE comments SET comment=:c WHERE id=:id");
    $u->execute([':c'=>$comment, ':id'=>$id]);
    $_SESSION['flash']['success']='Comment updated.';
    header('Location: '.url('admin/comments.php')); exit;
  }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h1 class="h3 mb-3">Edit Comment</h1>
  <?php if($err): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div><?php endif; ?>
  <div class="card p-4 shadow-sm" style="max-width:900px;">
    <div class="mb-2 small text-muted">On recipe: <strong><?= htmlspecialchars($c['recipe_title'] ?? '—') ?></strong></div>
    <div class="mb-2 small text-muted">By: <strong><?= htmlspecialchars($c['username'] ?? '—') ?></strong></div>
    <form method="post">
      <?php csrf_field(); ?>
      <label class="form-label">Comment</label>
      <textarea class="form-control mb-3" rows="5" name="comment"><?= htmlspecialchars($_POST['comment'] ?? $c['comment']) ?></textarea>
      <div class="d-flex gap-2">
        <button class="btn btn-primary">Save changes</button>
        <a class="btn btn-outline-secondary" href="<?= url('admin/comments.php') ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
