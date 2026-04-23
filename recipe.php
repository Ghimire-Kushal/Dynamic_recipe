<?php
require __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

if (!function_exists('is_logged_in')) {
  function is_logged_in(): bool { return !empty($_SESSION['user']); }
}
if (!function_exists('current_user_id')) {
  function current_user_id() { return $_SESSION['user']['id'] ?? null; }
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) { echo '<div class="card">Invalid recipe.</div>'; include 'includes/footer.php'; exit; }

$st = $pdo->prepare("SELECT r.*, u.username AS author FROM recipes r LEFT JOIN users u ON u.id=r.author_id WHERE r.id=?");
$st->execute([$id]);
$r = $st->fetch();
if(!$r){ echo '<div class="card">Recipe not found.</div>'; include 'includes/footer.php'; exit; }

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['comment']) && is_logged_in()){
  $c = trim($_POST['comment']);
  if($c!==''){
    $pdo->prepare("INSERT INTO comments (recipe_id, user_id, comment, created_at) VALUES (?,?,?,NOW())")->execute([$id, $_SESSION['user']['id'], $c]);
    header("Location: " . url('recipe.php?id=' . $id));
    exit;
  }
}

$cs = $pdo->prepare("SELECT c.*, u.username FROM comments c LEFT JOIN users u ON u.id=c.user_id WHERE c.recipe_id=? ORDER BY c.created_at DESC");
$cs->execute([$id]);
$comments = $cs->fetchAll();
?>
  <div class="grid" style="grid-template-columns:2fr 1fr; margin-top:16px;">
    <div>
      <h1><?php echo e($r['title']); ?></h1>
      <div class="badge"><?php echo e($r['category']); ?></div>
      <?php if($r['image']): ?><img src="<?php echo url('uploads/' . $r['image']); ?>" style="width:100%; max-height:420px; object-fit:cover; border-radius:12px; margin:12px 0;"><?php endif; ?>
      <h3>Ingredients</h3>
      <pre class="card" style="white-space:pre-wrap;"><?php echo e($r['ingredients']); ?></pre>
      <h3>Steps</h3>
      <div class="card"><?php echo nl2br(e($r['steps'])); ?></div>
    </div>
    <div>
      <div class="card">
        <h3>Leave a comment</h3>
        <?php if(is_logged_in()): ?>
        <form method="post">
          <textarea name="comment" rows="3" class="form-control" placeholder="Share your thoughts..."></textarea>
          <div class="form-actions"><button class="btn primary">Post</button></div>
        </form>
        <?php else: ?>
          <p>Please <a href="<?php echo url('auth/login.php'); ?>">log in</a> to comment.</p>
        <?php endif; ?>
      </div>
      <div class="card" style="margin-top:16px;">
        <h3>Comments</h3>
        <?php if(!$comments): ?>
          <p class="text-muted">No comments yet.</p>
        <?php else: foreach($comments as $c): ?>
          <div style="margin-bottom:12px;">
            <strong><?php echo e($c['username'] ?? 'User'); ?></strong>
            <div style="font-size:12px; color:#777;"><?php echo e($c['created_at']); ?></div>
            <div><?php echo nl2br(e($c['comment'])); ?></div>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
