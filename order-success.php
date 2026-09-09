<?php
require 'auth-check.php';
require 'db.php';

$order_id = $_GET['order_id'] ?? 0;

if (!$order_id) {
    header('Location: index.php');
    exit;
}

// 1. Fetch main order details by primary key 'id'
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// 2. Fetch purchased items joining with the products table
$stmtItems = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmtItems->execute([$order_id]);
$orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Florellea</title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        body {
            background-color: #faf8f5;
            font-family: sans-serif;
            color: #2b2b2b;
            margin: 0;
            padding: 40px 20px;
        }

        .receipt-container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            text-align: center;
        }

        .logo {
            font-family: serif;
            font-size: 28px;
            letter-spacing: 2px;
            color: #2b2b2b;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .success-icon {
            font-size: 40px;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        h1 {
            font-family: serif;
            font-size: 24px;
            font-weight: 400;
            margin-bottom: 10px;
        }

        p.sub-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .order-details {
            text-align: left;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .detail-row strong {
            color: #333;
        }

        .items-list {
            text-align: left;
            margin-bottom: 30px;
        }

        .items-list h3 {
            font-family: serif;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 8px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 10px;
            color: #555;
        }

        .btn-home {
            display: inline-block;
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

        .btn-home:hover {
            background: #000000;
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <a href="index.php" class="logo">florellea</a>
        
        <div class="success-icon">&#10004;</div>
        <h1>Thank You for Your Order!</h1>
        <p class="sub-text">We've received your order and are getting it ready for shipment.</p>

        <div class="order-details">
            <div class="detail-row">
                <span>Order ID:</span>
                <strong>#<?php echo htmlspecialchars($order['id']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Customer Name:</span>
                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Email:</span>
                <strong><?php echo htmlspecialchars($order['email']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Shipping Address:</span>
                <strong><?php echo htmlspecialchars($order['address']) . ', ' . htmlspecialchars($order['city']); ?></strong>
            </div>
            <div class="detail-row">
                <span>Date:</span>
                <strong><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></strong>
            </div>
        </div>

        <div class="items-list">
            <h3>Items Ordered</h3>
            <?php foreach ($orderItems as $item): ?>
                <div class="item-row">
                    <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>

            <div class="item-row" style="font-weight: bold; color: #2b2b2b; margin-top: 15px; border-top: 1px solid #f0f0f0; padding-top: 10px;">
                <span>Total Paid</span>
                <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>

        <a href="index.php" class="btn-home">Continue Shopping</a>
    </div>

</body>
</html>