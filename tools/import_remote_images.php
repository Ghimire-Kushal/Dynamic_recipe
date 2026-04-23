<?php
// tools/import_remote_images.php
require __DIR__ . '/../config/db.php';

$uploadDir = __DIR__ . '/../uploads/';
$basePath  = 'uploads/';

$stmt = $pdo->query("SELECT id, image FROM recipes WHERE image LIKE 'http%'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $id  = (int)$row['id'];
    $url = $row['image'];

    // guess extension
    $path = parse_url($url, PHP_URL_PATH);
    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg');

    $filename = "recipe{$id}." . $ext;
    $fullPath = $uploadDir . $filename;

    echo "Downloading $url → $filename<br>";

    $data = @file_get_contents($url);
    if ($data === false) {
        echo "❌ Failed<br>";
        continue;
    }

    file_put_contents($fullPath, $data);

    $update = $pdo->prepare("UPDATE recipes SET image = ? WHERE id = ?");
    $update->execute([$basePath . $filename, $id]);

    echo "✅ Saved and updated<br><br>";
}

echo "Done.";
