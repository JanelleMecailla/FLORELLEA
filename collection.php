<?php require 'auth-check.php'; ?>
<?php 
$pageTitle = 'Collection'; 
include 'header.php'; 
require 'db.php';

// Fetch all products from database
$stmt = $pdo->query("SELECT * FROM products ORDER BY name ASC");
$products = $stmt->fetchAll();
?>

<main style="padding: 120px 20px 60px; max-width: 1100px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 50px;">
        <h1 style="font-family: serif; font-size: 36px; margin-bottom: 10px;">The Full Collection</h1>
        <p style="font-size: 14px; color: #666;">Discover your signature scent from our artisanal lineup.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 40px;">
        <?php foreach ($products as $product): ?>
            <div style="text-align: center; border: 1px solid #f0f0f0; border-radius: 8px; padding: 20px; background: #fff; position: relative;">
                <a href="product-detail.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit;">
                    <div class="product-image <?php echo htmlspecialchars($product['bg_class'] ?? ''); ?>" style="padding: 30px; border-radius: 6px; margin-bottom: 15px; position: relative;">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 150%; height: 300px; object-fit: contain; <?php echo ($product['stock'] <= 0) ? 'opacity: 0.5; filter: grayscale(80%);' : ''; ?>">
                    </div>
                    <span style="font-size: 10px; letter-spacing: 1.5px; color: #d4a373; text-transform: uppercase;"><?php echo htmlspecialchars($product['category']); ?></span>
                    <h3 style="font-size: 18px; margin: 5px 0;"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p style="font-size: 12px; color: #888; margin-bottom: 10px;"><?php echo htmlspecialchars($product['notes']); ?></p>
                    <p style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">$<?php echo number_format($product['price'], 2); ?></p>
                </a>
                
                <!-- STOCK CHECK LOGIC -->
                <?php if ($product['stock'] > 0): ?>
                    <form action="cart-action.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" style="width: 100%; padding: 10px; background: #2b2b2b; color: white; border: none; border-radius: 4px; font-size: 11px; letter-spacing: 1px; cursor: pointer;">ADD TO BAG</button>
                    </form>
                <?php else: ?>
                    <div style="margin-bottom: 8px; font-size: 11px; font-weight: bold; color: #d9534f; letter-spacing: 1px;">
                        OUT OF STOCK
                    </div>
                    <button type="button" disabled style="width: 100%; padding: 10px; background: #cccccc; color: #666666; border: none; border-radius: 4px; font-size: 11px; letter-spacing: 1px; cursor: not-allowed;">UNAVAILABLE</button>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'footer.php'; ?>