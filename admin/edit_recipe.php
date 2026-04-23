
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
$st = $pdo->prepare("SELECT * FROM recipes WHERE id=?"); $st->execute([$id]);
$r = $st->fetch(PDO::FETCH_ASSOC);
if(!$r){ $_SESSION['flash']['danger']='Recipe not found.'; header('Location: '.url('admin/recipes.php')); exit; }

$err = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
  check_csrf();
  $title = trim($_POST['title'] ?? '');
  $category = trim($_POST['category'] ?? '');
  $ingredients = trim($_POST['ingredients'] ?? '');
  $steps = trim($_POST['steps'] ?? '');
  $imageName = $r['image'];

  if($title==='') $err[]='Title is required';
  if($ingredients==='') $err[]='Ingredients required';
  if($steps==='') $err[]='Steps required';

  if(!empty($_FILES['image']['name'])){
    $dir = __DIR__ . '/../uploads';
    if(!is_dir($dir)) mkdir($dir, 0775, true);
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, ['jpg','jpeg','png','gif','webp'])) $err[]='Invalid image type';
    if($_FILES['image']['error'] !== UPLOAD_ERR_OK) $err[]='Image upload failed';
    if(!$err){
      $new = uniqid('img_', true).'.'.$ext;
      if(move_uploaded_file($_FILES['image']['tmp_name'], $dir.'/'.$new)){
        if(!empty($imageName) && file_exists($dir.'/'.$imageName)) @unlink($dir.'/'.$imageName);
        $imageName = $new;
      }
    }
  }

  if(!$err){
    $u = $pdo->prepare("UPDATE recipes SET title=:t, category=:c, ingredients=:i, steps=:s, image=:img WHERE id=:id");
    $u->execute([':t'=>$title, ':c'=>$category, ':i'=>$ingredients, ':s'=>$steps, ':img'=>$imageName, ':id'=>$id]);
    $_SESSION['flash']['success']='Recipe updated.';
    header('Location: '.url('admin/recipes.php')); exit;
  }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h1 class="h3 mb-3">Edit Recipe</h1>
  <?php if($err): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm" style="max-width:900px;">
    <?php csrf_field(); ?>
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input class="form-control" name="title" value="<?= htmlspecialchars($_POST['title'] ?? $r['title']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Category</label>
      <input class="form-control" name="category" value="<?= htmlspecialchars($_POST['category'] ?? $r['category']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Ingredients</label>
      <textarea class="form-control" rows="5" name="ingredients"><?= htmlspecialchars($_POST['ingredients'] ?? $r['ingredients']) ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Steps</label>
      <textarea class="form-control" rows="6" name="steps"><?= htmlspecialchars($_POST['steps'] ?? $r['steps']) ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Image</label>
      <input type="file" class="form-control" name="image">
      <?php if(!empty($r['image'])): ?>
        <div class="mt-2"><img src="<?= url('uploads/'.$r['image']) ?>" style="max-width:220px; height:auto;"></div>
      <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary">Save changes</button>
      <a class="btn btn-outline-secondary" href="<?= url('admin/recipes.php') ?>">Cancel</a>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
