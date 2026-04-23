<?php
require __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

if (!function_exists('is_logged_in')) {
  function is_logged_in(): bool { return !empty($_SESSION['user']); }
}
if (!function_exists('current_user_id')) {
  function current_user_id() { return $_SESSION['user']['id'] ?? null; }
}


if(!is_logged_in()){
  $_SESSION['flash']['danger'] = 'Please log in to add a recipe.';
  header('Location: ' . url('auth/login.php')); exit;
}

$err = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $title = trim($_POST['title'] ?? '');
  $category = trim($_POST['category'] ?? '');
  $ingredients = trim($_POST['ingredients'] ?? '');
  $steps = trim($_POST['steps'] ?? '');
  $imageName = null;

  if($title==='') $err[]='Title is required';
  if(strlen($title) > 150) $err[]='Title is too long';
  if($ingredients==='') $err[]='Ingredients are required';
  if($steps==='') $err[]='Steps are required';

  // Handle image upload
  if(!empty($_FILES['image']['name'])){
    $dir = __DIR__ . '/uploads';
    if(!is_dir($dir)) { mkdir($dir, 0775, true); }
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if(!in_array($ext, $allowed)) $err[]='Image must be JPG, PNG, GIF, or WEBP';
    if($_FILES['image']['error'] !== UPLOAD_ERR_OK) $err[]='Upload failed';
    if(empty($err)){
      $imageName = uniqid('img_', true) . '.' . $ext;
      move_uploaded_file($_FILES['image']['tmp_name'], $dir . '/' . $imageName);
    }
  }

  if(!$err){
    $st = $pdo->prepare("INSERT INTO recipes (title, category, ingredients, steps, image, author_id) VALUES (:t,:c,:i,:s,:img,:a)");
    $st->execute([
      ':t'=>$title, ':c'=>$category, ':i'=>$ingredients, ':s'=>$steps, ':img'=>$imageName, ':a'=>current_user_id()
    ]);
    $_SESSION['flash']['success']='Recipe added!';
    header('Location: ' . url('index.php')); exit;
  }
}
?>
<h1 class="h3 mb-3">Add Recipe</h1>
<?php if($err): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="card shadow-sm border-0 rounded-4 p-4" style="max-width:800px;">
  <div class="mb-3">
    <label class="form-label">Title</label>
    <input class="form-control" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"/>
  </div>
  <div class="mb-3">
    <label class="form-label">Category</label>
    <input class="form-control" name="category" placeholder="e.g., breakfast, dinner" value="<?= htmlspecialchars($_POST['category'] ?? '') ?>"/>
  </div>
  <div class="mb-3">
    <label class="form-label">Ingredients</label>
    <textarea class="form-control" rows="5" name="ingredients"><?= htmlspecialchars($_POST['ingredients'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Steps</label>
    <textarea class="form-control" rows="6" name="steps"><?= htmlspecialchars($_POST['steps'] ?? '') ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Image</label>
    <input type="file" class="form-control" name="image" />
    <div class="form-text">JPG, PNG, GIF, WEBP</div>
  </div>
  <div class="d-grid">
    <button class="btn btn-primary">Save</button>
  </div>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
