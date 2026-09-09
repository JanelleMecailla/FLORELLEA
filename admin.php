<?php
session_start();
require 'db.php';

// Auth Guard
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

// --- Fetch Unread Messages Count for Header Badge ---
try {
    $unread_stmt = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0");
    $unread_count = $unread_stmt->fetchColumn();
} catch (PDOException $e) {
    $unread_count = 0; // Fallback if messages table doesn't exist yet
}

$message = '';
$error = '';

// Handle Delete Request
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: admin.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        $error = "Could not delete product. It may be linked to active orders.";
    }
}

// Handle Add Product Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $notes    = trim($_POST['notes'] ?? '');
    $price    = (float)($_POST['price'] ?? 0);
    $stock    = (int)($_POST['stock'] ?? 0);
    $bg_class = $_POST['bg_class'] ?? 'pink-product';
    $imageName = 'blush-bloom.jpg'; // default fallback

    // --- Server-Side Validation ---
    if (empty($name) || empty($category) || empty($notes)) {
        $error = "All textual fields are required.";
    } elseif ($price <= 0) {
        $error = "Price must be greater than $0.00.";
    } elseif ($stock < 0) {
        $error = "Stock quantity cannot be negative.";
    } else {
        // Image Upload Handler & Validation
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);

            if (in_array($fileType, $allowedTypes)) {
                $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES['image']['name']));
                if (move_uploaded_file($_FILES['image']['tmp_name'], './images/' . $fileName)) {
                    $imageName = $fileName;
                }
            } else {
                $error = "Invalid file type. Only JPG, PNG, and WEBP images are allowed.";
            }
        }

        // --- Database Execution with Error Handling ---
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, category, notes, price, stock, image, bg_class) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $category, $notes, $price, $stock, $imageName, $bg_class]);
                $message = "Product added successfully!";
            } catch (PDOException $e) {
                $error = "Database error: Could not add product.";
            }
        }
    }
}

// Fetch All Products
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    $error = "Failed to load product inventory.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Florellea</title>
    <style>
        body { font-family: sans-serif; background: #faf7f5; margin: 0; padding: 40px 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .admin-nav { display: flex; gap: 10px; align-items: center; }
        .card { background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 11px; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 20px; background: #2b2b2b; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; }
        .btn-danger { background: #d9534f; }
        .btn-edit { background: #e0a96d; }
        .btn-msg { background: #f3a6b8; color: #fff; font-weight: bold; }
        .badge { background: #e74c3c; color: white; border-radius: 10px; padding: 2px 7px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 13px; }
        th { background: #f4f4f4; font-size: 11px; }
        .stock-badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-weight: bold; font-size: 12px; }
        .stock-ok { background: #e8f5e9; color: #2e7d32; }
        .stock-low { background: #ffebee; color: #c62828; }
        .alert-success { padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px; }
        .alert-error { padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="font-family: serif;">Store Management</h1>
            <div class="admin-nav">
                <!-- Messages Button with Red Notification Badge -->
                <a href="admin-messages.php" class="btn btn-msg">
                    Messages 
                    <?php if ($unread_count > 0): ?>
                        <span class="badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <a href="admin-orders.php" class="btn" style="background: #e0a96d;">Orders</a>
               <a href="index.php" class="btn" style="background:#666;" target="_blank">View Website</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if ($message || isset($_GET['msg'])): ?>
            <div class="alert-success">
                <?php echo htmlspecialchars($message ? $message : 'Operation completed successfully.'); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Add Product Form -->
        <div class="card">
            <h3>Add New Fragrance</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_product" value="1">
                <div class="form-grid">
                    <div class="form-group">
                        <label>PRODUCT NAME</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>CATEGORY</label>
                        <select name="category" required>
                            <option value="floral">Floral</option>
                            <option value="fresh">Fresh</option>
                            <option value="warm">Warm</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>NOTES / SCENT PROFILE</label>
                        <input type="text" name="notes" placeholder="e.g. Floral · Soft · Romantic" required>
                    </div>
                    <div class="form-group">
                        <label>PRICE ($)</label>
                        <input type="number" step="0.01" name="price" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>INITIAL STOCK QUANTITY</label>
                        <input type="number" name="stock" value="50" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>CARD BACKGROUND THEME</label>
                        <select name="bg_class">
                            <option value="pink-product">Pink Theme</option>
                            <option value="green-product">Green Theme</option>
                            <option value="cream-product">Cream Theme</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>IMAGE FILE</label>
                        <input type="file" name="image" accept="image/*" required>
                    </div>
                </div>
                <button type="submit" class="btn" style="margin-top: 10px;">ADD PRODUCT</button>
            </form>
        </div>

        <!-- Product Table -->
        <div class="card">
            <h3>Current Inventory</h3>
            <table>
                <thead>
                    <tr>
                        <th>IMAGE</th>
                        <th>NAME</th>
                        <th>CATEGORY</th>
                        <th>PRICE</th>
                        <th>STOCK</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="6" style="text-align:center;">No products found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <img src="images/<?php echo htmlspecialchars($p['image']); ?>" width="40" height="40" style="object-fit:cover; border-radius: 4px;">
                                </td>
                                <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                                <td><?php echo strtoupper(htmlspecialchars($p['category'])); ?></td>
                                <td>$<?php echo number_format($p['price'], 2); ?></td>
                                <td>
                                    <?php $stockVal = (int)($p['stock'] ?? 0); ?>
                                    <span class="stock-badge <?php echo $stockVal <= 5 ? 'stock-low' : 'stock-ok'; ?>">
                                        <?php echo $stockVal; ?> pcs
                                    </span>
                                </td>
                                <td>
                                    <a href="admin-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-edit">Edit</a>
                                    <a href="admin.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>