<?php
require __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

$cats = $pdo->query("
    SELECT category, COUNT(*) AS cnt
    FROM recipes
    WHERE category IS NOT NULL AND category <> ''
    GROUP BY category
    ORDER BY category
")->fetchAll();
?>

<h2 class="text" style="text-align:center; margin-top:20px; text-decoration:underline;">
    RECIPES
</h2>

<div class="container my-4"><!-- same container width as index.php -->

    <?php foreach ($cats as $row): ?>
        <?php
            $cat = $row['category'];
            $st = $pdo->prepare("
                SELECT id, title, image, steps, category
                FROM recipes
                WHERE category = ?
                ORDER BY created_at DESC
                LIMIT 12
            ");
            $st->execute([$cat]);
            $items = $st->fetchAll();
            if (!$items) continue;
        ?>

        <!-- Category heading -->
        <h3 class="text mt-4 mb-3"><?php echo e(ucfirst($cat)); ?></h3>

        <!-- Cards in a grid (same structure as index.php) -->
        <div class="row g-4 mb-4">
            <?php foreach ($items as $r): ?>
                <?php
                    $desc = trim(strip_tags($r['steps']));
                    if (mb_strlen($desc) > 120) {
                        $desc = mb_substr($desc, 0, 120) . '...';
                    }
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">

                        <?php if (!empty($r['image'])): ?>
                            <!-- Image behaves exactly like on index.php -->
                            <img
                                src="<?php echo e(url('uploads/' . $r['image'])); ?>"
                                class="card-img-top"
                                alt="Recipe image"
                                style="height:220px;object-fit:cover;width:100%;"
                            >
                        <?php else: ?>
                            <!-- Placeholder when there is no image -->
                            <div
                              class="bg-secondary d-flex align-items-center justify-content-center text-white"
                              style="height:220px;width:100%;"
                            >
                                <span>Recipe image</span>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <span class="badge bg-primary mb-2">
                                <?php echo e(ucfirst($cat)); ?>
                            </span>

                            <h5 class="card-title mb-2">
                                <?php echo e($r['title']); ?>
                            </h5>

                            <p class="card-text text-muted small mb-0">
                                <?php echo e($desc); ?>
                            </p>
                        </div>

                        <div class="card-footer bg-white border-0 pb-3 pt-0">
                            <a
                              class="btn btn-outline-primary w-100"
                              href="<?php echo e(url('recipe.php?id=' . $r['id'])); ?>"
                            >
                                View Recipe
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endforeach; ?>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
