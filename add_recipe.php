<?php
// START SESSION FIRST
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

/* ---------------- AUTH HELPERS ---------------- */
if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool {
        return isset($_SESSION['user']['id']);
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id(): ?int {
        return $_SESSION['user']['id'] ?? null;
    }
}

/* ---------------- REQUIRE LOGIN ---------------- */
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

        $_SESSION['flash']['success'] = 'Recipe added successfully!';
        header('Location: ' . url('index.php'));
        exit;
    }
}
?>

<!-- ================= PAGE UI ================= -->

<h1 class="h3 mb-3">Add Recipe</h1>

<?php if ($errors): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data"
      class="card shadow-sm border-0 rounded-4 p-4"
      style="max-width:800px;">

    <div class="mb-3">
        <label class="form-label">Title</label>
        <input class="form-control" name="title"
               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Category</label>
        <input class="form-control" name="category"
               placeholder="e.g. breakfast, dinner"
               value="<?= htmlspecialchars($_POST['category'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Ingredients</label>
        <textarea class="form-control" rows="5"
                  name="ingredients"><?= htmlspecialchars($_POST['ingredients'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Steps</label>
        <textarea class="form-control" rows="6"
                  name="steps"><?= htmlspecialchars($_POST['steps'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Image (optional)</label>
        <input type="file" class="form-control" name="image">
        <div class="form-text">JPG, PNG, GIF, WEBP</div>
    </div>

    <div class="d-grid">
        <button class="btn btn-primary">Save Recipe</button>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
