<?php
session_start();
require 'db.php';

// Auth Guard
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

$error = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: admin.php');
    exit;
}

// Fetch Product safely
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        header('Location: admin.php?msg=notfound');
        exit;
    }
} catch (PDOException $e) {
    die("Database query error.");
}

// Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $notes    = trim($_POST['notes'] ?? '');
    $price    = (float)($_POST['price'] ?? 0);
    $stock    = (int)($_POST['stock'] ?? 0);
    $bg_class = $_POST['bg_class'] ?? 'pink-product';
    $imageName = $product['image']; // Default to keeping existing image

    // Server-Side Validation
    if (empty($name) || empty($category) || empty($notes)) {
        $error = "All textual fields are required.";
    } elseif ($price <= 0) {
        $error = "Price must be greater than $0.00.";
    } elseif ($stock < 0) {
        $error = "Stock quantity cannot be negative.";
    } else {
        // Image Upload (Optional Update)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);

            if (in_array($fileType, $allowedTypes)) {
                $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES['image']['name']));
                if (move_uploaded_file($_FILES['image']['tmp_name'], './images/' . $fileName)) {
                    $imageName = $fileName;
                } else {
                    $error = "Failed to upload new image file.";
                }
            } else {
                $error = "Invalid image file type. Only JPG, PNG, and WEBP are permitted.";
            }
        }

        // Database Update
        if (empty($error)) {
            try {
                $updateStmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, notes = ?, price = ?, stock = ?, bg_class = ?, image = ? WHERE id = ?");
                $updateStmt->execute([$name, $category, $notes, $price, $stock, $bg_class, $imageName, $id]);

                header('Location: admin.php?msg=updated');
                exit;
            } catch (PDOException $e) {
                $error = "Database error: Could not update product details.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - Admin</title>
    <style>
        body { font-family: sans-serif; background: #faf7f5; padding: 40px; }
        .card { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #eee; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 20px; background: #2b2b2b; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .alert-error { padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 15px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Edit Fragrance Details</h2>

        <?php if ($error): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>PRODUCT NAME</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>CATEGORY</label>
                <select name="category" required>
                    <option value="floral" <?php if($product['category'] === 'floral') echo 'selected'; ?>>Floral</option>
                    <option value="fresh" <?php if($product['category'] === 'fresh') echo 'selected'; ?>>Fresh</option>
                    <option value="warm" <?php if($product['category'] === 'warm') echo 'selected'; ?>>Warm</option>
                </select>
            </div>
            <div class="form-group">
                <label>NOTES</label>
                <input type="text" name="notes" value="<?php echo htmlspecialchars($product['notes']); ?>" required>
            </div>
            <div class="form-group">
                <label>PRICE ($)</label>
                <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" min="0.01" required>
            </div>
            <div class="form-group">
                <label>STOCK QUANTITY</label>
                <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock'] ?? 0); ?>" min="0" required>
            </div>
            <div class="form-group">
                <label>CARD BACKGROUND THEME</label>
                <select name="bg_class">
                    <option value="pink-product" <?php if(($product['bg_class'] ?? '') === 'pink-product') echo 'selected'; ?>>Pink Theme</option>
                    <option value="green-product" <?php if(($product['bg_class'] ?? '') === 'green-product') echo 'selected'; ?>>Green Theme</option>
                    <option value="cream-product" <?php if(($product['bg_class'] ?? '') === 'cream-product') echo 'selected'; ?>>Cream Theme</option>
                </select>
            </div>
            <div class="form-group">
                <label>CHANGE IMAGE (OPTIONAL)</label>
                <input type="file" name="image" accept="image/*">
                <p style="font-size: 11px; color: #888;">Current image: <?php echo htmlspecialchars($product['image']); ?></p>
            </div>
            <button type="submit" class="btn">UPDATE PRODUCT</button>
            <a href="admin.php" style="margin-left: 10px; font-size: 12px; color: #666; text-decoration: none;">Cancel</a>
        </form>
    </div>
</body>
</html>