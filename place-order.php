<?php
require 'auth-check.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Get customer details from form post or user session
$customer_name   = trim($_POST['customer_name'] ?? $_SESSION['user_name'] ?? 'Guest Customer');
$email           = trim($_POST['email'] ?? $_SESSION['user_email'] ?? 'guest@example.com');
$address         = trim($_POST['shipping_address'] ?? $_POST['address'] ?? '');
$city            = trim($_POST['city'] ?? 'Dumaguete City');
$postal_code     = trim($_POST['postal_code'] ?? '6220');

if (empty($address)) {
    $_SESSION['checkout_error'] = "Please provide a valid shipping address.";
    header('Location: checkout.php');
    exit;
}

$productIds = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));

// Fetch product details
$stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id IN ($placeholders)");
$stmt->execute($productIds);
$dbProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$productMap = [];
foreach ($dbProducts as $prod) {
    $productMap[$prod['id']] = $prod;
}

// 1. Check stock availability
foreach ($_SESSION['cart'] as $productId => $quantity) {
    if (!isset($productMap[$productId])) {
        $_SESSION['checkout_error'] = "One of the items in your cart is no longer available.";
        header('Location: cart.php');
        exit;
    }

    $product = $productMap[$productId];
    if (isset($product['stock']) && $product['stock'] < $quantity) {
        $_SESSION['checkout_error'] = "Sorry, '{$product['name']}' only has {$product['stock']} item(s) left in stock.";
        header('Location: cart.php');
        exit;
    }
}

// Calculate total
$totalAmount = 0;
$orderItemsData = [];

foreach ($_SESSION['cart'] as $productId => $quantity) {
    $price = $productMap[$productId]['price'];
    $subtotal = $price * $quantity;
    $totalAmount += $subtotal;

    $orderItemsData[] = [
        'product_id' => $productId,
        'quantity'   => $quantity,
        'price'      => $price
    ];
}

try {
    $pdo->beginTransaction();

    // 2. Insert into orders table matching your exact columns:
    // id, customer_name, email, address, city, postal_code, total_amount, created_at
    $stmtOrder = $pdo->prepare("
        INSERT INTO orders (customer_name, email, address, city, postal_code, total_amount) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtOrder->execute([
        $customer_name,
        $email,
        $address,
        $city,
        $postal_code,
        $totalAmount
    ]);

    $order_id = $pdo->lastInsertId();

    // 3. Insert items into order_items
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    // Check if stock column exists before updating
    $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

    foreach ($orderItemsData as $item) {
        $stmtItem->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        ]);

        // Reduce stock
        try {
            $stmtUpdateStock->execute([
                $item['quantity'],
                $item['product_id'],
                $item['quantity']
            ]);
        } catch (Exception $e) {
            // Ignore if stock column doesn't exist yet
        }
    }

    $pdo->commit();
    unset($_SESSION['cart']);

    header("Location: order-success.php?order_id=" . $order_id);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['checkout_error'] = "Database Error: " . $e->getMessage();
    header('Location: checkout.php');
    exit;
}
?>