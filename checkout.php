<?php
require 'auth-check.php';
require 'db.php';

// Redirect back to cart if the cart is empty
if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Fetch products in cart to calculate total price
$productIds = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));

$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($productIds);
$products = $stmt->fetchAll();

$grandTotal = 0;
$cartSummary = [];

foreach ($products as $product) {
    $qty = $_SESSION['cart'][$product['id']];
    $subtotal = $product['price'] * $qty;
    $grandTotal += $subtotal;
    $cartSummary[] = [
        'name' => $product['name'],
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Florellea</title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        body {
            background-color: var(--color-bg-light, #faf8f5);
            font-family: var(--font-body, sans-serif);
            color: #2b2b2b;
            margin: 0;
        }

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

        .checkout-wrapper {
            max-width: 900px;
            margin: 50px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
        }

        .checkout-box, .summary-box {
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }

        h2 {
            font-family: var(--font-heading, serif);
            font-size: 24px;
            font-weight: 400;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
        }

        textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            box-sizing: border-box;
            background: #fafafa;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: #2b2b2b;
            color: #ffffff;
            border: none;
            border-radius: 50px;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #000000;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 12px;
            color: #555;
        }

        .summary-total {
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-family: var(--font-heading, serif);
            font-size: 20px;
            font-weight: bold;
            color: #2b2b2b;
        }

        .error-message {
            background: #fdf0ed;
            color: #d9534f;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .checkout-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="index.php" class="logo">florellea</a>
        <a href="cart.php" style="font-size: 11px; letter-spacing: 1.5px; text-decoration: none; color: #555;">&larr; BACK TO CART</a>
    </header>

    <div class="checkout-wrapper">
        <!-- FORM SECTION -->
        <div class="checkout-box">
            <h2>Shipping & Payment Details</h2>

            <?php if (isset($_SESSION['checkout_error'])): ?>
                <div class="error-message">
                    <?php 
                        echo htmlspecialchars($_SESSION['checkout_error']); 
                        unset($_SESSION['checkout_error']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="place-order.php" method="POST">
    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="customer_name" required value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label>Shipping Address</label>
        <textarea name="shipping_address" placeholder="Street Address" required></textarea>
    </div>

    <div class="form-group">
        <label>City</label>
        <input type="text" name="city" value="Dumaguete City" required>
    </div>

    <div class="form-group">
        <label>Postal Code</label>
        <input type="text" name="postal_code" value="6220" required>
    </div>

    <button type="submit" class="btn-submit">Place Order</button>
</form>
        </div>

        <!-- SUMMARY SECTION -->
        <div class="summary-box">
            <h2>Order Summary</h2>
            <?php foreach ($cartSummary as $item): ?>
                <div class="summary-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['qty']; ?>)</span>
                    <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
                </div>
            <?php endforeach; ?>

            <div class="summary-total">
                <span>Total Amount</span>
                <span>$<?php echo number_format($grandTotal, 2); ?></span>
            </div>
        </div>
    </div>

</body>
</html>