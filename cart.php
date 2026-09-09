<?php
require 'auth-check.php';
require 'db.php';

$cartItems = [];
$grandTotal = 0;

// Fetch product details from DB if cart has items
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    
    if (count($productIds) > 0) {
        // Create placeholders for SQL IN clause (?, ?, ?)
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $products = $stmt->fetchAll();

        foreach ($products as $product) {
            $id = $product['id'];
            
            // Handle session cart structure whether stored as array or int
            $quantity = is_array($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id]['quantity'] : $_SESSION['cart'][$id];
            
            $subtotal = $product['price'] * $quantity;
            $grandTotal += $subtotal;

            $cartItems[] = [
                'id'       => $id,
                'name'     => $product['name'],
                'price'    => $product['price'],
                'image'    => $product['image'],
                'stock'    => $product['stock'] ?? 0,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - Florellea</title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        body {
            background-color: var(--color-bg-light, #faf8f5);
            margin: 0;
            font-family: var(--font-body, sans-serif);
            color: #333;
        }

        /* HEADER / NAV */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: #ffffff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .logo {
            font-family: var(--font-heading, serif);
            font-size: 26px;
            letter-spacing: 2px;
            color: #2b2b2b;
            text-decoration: none;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #555;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* CART CONTAINER */
        .cart-container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 20px;
        }
        .cart-title {
            font-family: var(--font-heading, serif);
            font-size: 36px;
            font-weight: 400;
            text-align: center;
            margin-bottom: 40px;
            color: #2b2b2b;
        }

        /* ALERT MESSAGES */
        .alert-box {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 13px;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* TABLE STYLING */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .cart-table th {
            background: #f8f6f2;
            padding: 16px 20px;
            text-align: left;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #666;
        }
        .cart-table td {
            padding: 20px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .product-info img {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .product-name {
            font-family: var(--font-heading, serif);
            font-size: 18px;
            color: #2b2b2b;
            margin: 0;
        }

        /* QUANTITY INPUT & BUTTONS */
        .qty-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .qty-input {
            width: 50px;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            text-align: center;
            font-size: 14px;
        }
        .btn-update {
            background: #f0f0f0;
            border: 1px solid #ddd;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
        }
        .btn-remove {
            color: #e74c3c;
            background: none;
            border: none;
            font-size: 12px;
            cursor: pointer;
            text-decoration: underline;
        }

        /* SUMMARY / CHECKOUT BOX */
        .cart-summary {
            margin-top: 30px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            border: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total-price {
            font-family: var(--font-heading, serif);
            font-size: 28px;
            color: #2b2b2b;
        }
        .checkout-actions {
            display: flex;
            gap: 15px;
        }
        .btn-continue {
            padding: 14px 28px;
            border: 1px solid #2b2b2b;
            color: #2b2b2b;
            text-decoration: none;
            border-radius: 50px;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .btn-checkout {
            padding: 14px 32px;
            background: #2b2b2b;
            color: #ffffff;
            text-decoration: none;
            border-radius: 50px;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
            transition: background 0.2s ease;
        }
        .btn-checkout:hover {
            background: #000000;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .empty-cart h3 {
            font-family: var(--font-heading, serif);
            font-size: 24px;
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <a href="index.php" class="logo">florellea</a>
        <nav>
            <a href="index.php">HOME</a>
            <a href="about.php">ABOUT</a>
            <a href="collection.php">COLLECTION</a>
            <a href="story.php">STORY</a>
        </nav>
        <div>
            <a href="user-logout.php" style="font-size: 11px; letter-spacing: 1px; color: #888; text-decoration: none;">LOGOUT</a>
        </div>
    </header>

    <div class="cart-container">
        <h1 class="cart-title">Your Shopping Bag</h1>

        <!-- FLASH MESSAGES FROM CART-ACTION -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-box alert-error">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-box alert-success">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <h3>Your cart is currently empty</h3>
                <a href="collection.php" class="btn-checkout">EXPLORE COLLECTION</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" onerror="this.src='https://via.placeholder.com/65';">
                                    <div>
                                        <p class="product-name"><?php echo htmlspecialchars($item['name']); ?></p>
                                        <small style="color: #888; font-size: 11px;">In Stock: <?php echo $item['stock']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form action="cart-action.php" method="POST" class="qty-form">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="qty-input">
                                    <button type="submit" class="btn-update">Update</button>
                                </form>
                            </td>
                            <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                            <td>
                                <form action="cart-action.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn-remove">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div>
                    <span style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #777;">Grand Total</span>
                    <div class="total-price">$<?php echo number_format($grandTotal, 2); ?></div>
                </div>

                <div class="checkout-actions">
                    <a href="collection.php" class="btn-continue">CONTINUE SHOPPING</a>
                    <a href="checkout.php" class="btn-checkout">PROCEED TO CHECKOUT</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>