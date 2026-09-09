<?php
session_start();
require 'db.php';

// Auth Guard
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

// Fetch Orders
try {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
    $error = "Could not retrieve orders.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Management - Florellea</title>
    <style>
        body { font-family: sans-serif; background: #faf7f5; margin: 0; padding: 40px 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card { background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 30px; }
        
        .btn { padding: 8px 16px; background: #2b2b2b; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 13px; }
        th { background: #f4f4f4; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }

        .alert-error { padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="font-family: serif; margin:0;">Order Management</h1>
            <a href="admin.php" class="btn">&larr; Back to Dashboard</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card">
            <h3 style="margin-top:0; margin-bottom:20px;">Customer Orders</h3>
            <table>
                <thead>
                    <tr>
                        <th>ORDER ID</th>
                        <th>CUSTOMER</th>
                        <th>EMAIL</th>
                        <th>TOTAL AMOUNT</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="5" style="text-align:center;">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($order['id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['customer_name'] ?? $order['name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_email'] ?? $order['email'] ?? 'N/A'); ?></td>
                                <td>$<?php echo number_format($order['total_amount'] ?? $order['total'] ?? 0, 2); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>