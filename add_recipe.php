<?php
// Redirect BEFORE any output
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require __DIR__ . '/config/db.php';

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool { return isset($_SESSION['user']['id']); }
}
if (!function_exists('current_user_id')) {
    function current_user_id(): ?int { return $_SESSION['user']['id'] ?? null; }
}

if (!is_logged_in()) {
    $_SESSION['flash']['danger'] = 'Please log in to add a recipe.';
    header('Location: ' . url('auth/login.php'));
    exit;
}

/* ---------------- FORM LOGIC ---------------- */
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $steps       = trim($_POST['steps'] ?? '');
    $imageName   = null;

    // Validation
    if ($title === '')              $errors[] = 'Title is required.';
    if (strlen($title) > 150)       $errors[] = 'Title must be under 150 characters.';
    if ($ingredients === '')        $errors[] = 'Ingredients are required.';
    if ($steps === '')              $errors[] = 'Steps are required.';

    /* ---------------- IMAGE UPLOAD ---------------- */
    if (!empty($_FILES['image']['name'])) {

        if (!is_uploaded_file($_FILES['image']['tmp_name'])) {
            $errors[] = 'Invalid image upload.';
        } else {
            $uploadDir = __DIR__ . '/uploads';

            // Ensure directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Allowed extensions
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];

            if (!in_array($ext, $allowed)) {
                $errors[] = 'Image must be JPG, PNG, GIF, or WEBP.';
            }

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Image upload failed (error code ' . $_FILES['image']['error'] . ').';
            }

            // Save file
            if (!$errors) {
                $imageName = uniqid('img_', true) . '.' . $ext;

                if (!move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    $uploadDir . '/' . $imageName
                )) {
                    $errors[] = 'Failed to save image file.';
                }
            }
        }
    }

    /* ---------------- SAVE TO DATABASE ---------------- */
    if (!$errors) {
        $stmt = $pdo->prepare("
            INSERT INTO recipes 
                (title, category, ingredients, steps, image, author_id)
            VALUES 
                (:title, :category, :ingredients, :steps, :image, :author)
        ");

        $stmt->execute([
            ':title'       => $title,
            ':category'    => $category,
            ':ingredients' => $ingredients,
            ':steps'       => $steps,
            ':image'       => $imageName,
            ':author'      => current_user_id()
        ]);

        $_SESSION['flash']['success'] = 'Recipe added! 🎉';
        header('Location: ' . url('index.php'));
        exit;
    }
}

// Safe to output HTML now
include __DIR__ . '/includes/header.php';
?>

<!-- Page intro banner -->
<section class="page-intro">
  <div class="container">
    <h1><i class="fa-solid fa-plus me-2" style="opacity:.85;"></i>Add a Recipe</h1>
    <p>Share your favourite dish with the community</p>
  </div>
</section>

<!-- Centered form -->
<div style="padding: 60px 16px 80px; display:flex; justify-content:center;">
  <div style="width:100%; max-width:720px;">

    <?php if ($errors): ?>
    <div class="flash-alert danger" style="margin-bottom:24px;">
      <i class="fa-solid fa-circle-xmark" style="flex-shrink:0;"></i>
      <ul style="margin:0; padding-left:16px;">
        <?php foreach ($errors as $err): ?>
          <li><?= e($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

      <!-- Card: Basic info -->
      <div class="detail-card" style="margin-bottom:24px;">
        <h3 style="margin-bottom:20px;"><i class="fa-solid fa-circle-info"></i> Basic Info</h3>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">

          <div style="grid-column:1/-1;">
            <label class="form-label-custom">Recipe Title <span style="color:#ef4444;">*</span></label>
            <input class="form-input-custom" type="text" name="title"
                   placeholder="e.g. Creamy Butter Chicken"
                   value="<?= e($_POST['title'] ?? '') ?>" required>
          </div>

          <div>
            <label class="form-label-custom">Category</label>
            <input class="form-input-custom" type="text" name="category"
                   list="cat-suggestions"
                   placeholder="e.g. dinner, breakfast"
                   value="<?= e($_POST['category'] ?? '') ?>">
            <datalist id="cat-suggestions">
              <?php foreach(['breakfast','lunch','dinner','dessert','snack','pizza','pasta',
                             'soup','salad','healthy','fast food','nepali','indian','baking'] as $sug): ?>
                <option value="<?= $sug ?>">
              <?php endforeach; ?>
            </datalist>
            <div style="font-size:.75rem;color:var(--text-light);margin-top:6px;">
              Start typing for suggestions
            </div>
          </div>

          <div>
            <label class="form-label-custom">Image <span style="color:var(--text-light);font-weight:400;">(optional)</span></label>
            <label id="imgLabel" style="display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--bg-input);border:1.5px dashed var(--border-clr);border-radius:var(--r-lg);cursor:pointer;transition:border-color var(--t-fast);">
              <i class="fa-solid fa-cloud-arrow-up" style="color:var(--clr-500);font-size:1.1rem;"></i>
              <span id="imgLabelText" style="font-size:.85rem;color:var(--text-muted);">Click to upload photo</span>
              <input type="file" name="image" id="imgInput" accept=".jpg,.jpeg,.png,.gif,.webp"
                     style="display:none;" onchange="previewImg(this)">
            </label>
            <div style="font-size:.74rem;color:var(--text-light);margin-top:5px;">JPG · PNG · GIF · WEBP</div>
            <!-- Image preview -->
            <img id="imgPreview" src="" alt=""
                 style="display:none;margin-top:12px;width:100%;max-height:180px;object-fit:cover;border-radius:var(--r-lg);">
          </div>

        </div>
      </div>

      <!-- Card: Ingredients -->
      <div class="detail-card" style="margin-bottom:24px;">
        <h3 style="margin-bottom:6px;"><i class="fa-solid fa-list-check"></i> Ingredients <span style="color:#ef4444;">*</span></h3>
        <p style="font-size:.8rem;color:var(--text-light);margin:0 0 16px;">
          One ingredient per line — e.g. "2 cups flour"
        </p>
        <textarea class="form-input-custom" name="ingredients" rows="7"
                  placeholder="2 cups all-purpose flour&#10;1 tsp salt&#10;3 tbsp butter&#10;..."
                  style="resize:vertical;"
                  required><?= e($_POST['ingredients'] ?? '') ?></textarea>
      </div>

      <!-- Card: Cooking steps -->
      <div class="detail-card" style="margin-bottom:32px;">
        <h3 style="margin-bottom:6px;"><i class="fa-solid fa-shoe-prints"></i> Cooking Steps <span style="color:#ef4444;">*</span></h3>
        <p style="font-size:.8rem;color:var(--text-light);margin:0 0 16px;">
          Add one step per line.
        </p>
        <textarea class="form-input-custom" name="steps" rows="8"
                  placeholder="Preheat oven to 180°C.&#10;Mix dry ingredients in a bowl.&#10;Add wet ingredients and stir until smooth.&#10;..."
                  style="resize:vertical;"
                  required><?= e($_POST['steps'] ?? '') ?></textarea>
      </div>

      <!-- Actions -->
      <div style="display:flex; gap:12px; justify-content:flex-end; flex-wrap:wrap;">
        <a href="<?= url('index.php') ?>" class="btn-secondary-custom">
          <i class="fa-solid fa-xmark"></i> Cancel
        </a>
        <button type="submit" class="btn-primary-custom" style="padding:13px 36px;">
          <i class="fa-solid fa-floppy-disk"></i> Publish Recipe
        </button>
      </div>

    </form>
  </div>
</div>

<script>
function previewImg(input) {
  var label   = document.getElementById('imgLabelText');
  var preview = document.getElementById('imgPreview');
  if (input.files && input.files[0]) {
    label.textContent = input.files[0].name;
    var reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
