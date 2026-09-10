<?php
session_start();
require 'db.php';

// Auth Guard
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

// Handle Status Update Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status   = trim($_POST['status']);

    try {
        $stmtUpdate = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($stmtUpdate->execute([$status, $order_id])) {
            $_SESSION['status_msg'] = "Order status updated successfully!";
            $_SESSION['status_type'] = "success";
        } else {
            $_SESSION['status_msg'] = "Failed to update order status.";
            $_SESSION['status_type'] = "error";
        }
    } catch (PDOException $e) {
        $_SESSION['status_msg'] = "Database error: " . $e->getMessage();
        $_SESSION['status_type'] = "error";
    }

    header('Location: admin-orders.php');
    exit;
}

// Fetch Orders
try {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
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
        body { font-family: sans-serif; background: #faf7f5; margin: 0; padding: 40px 20px; color: #2b2b2b; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card { background: #fff; padding: 25px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        
        .btn { padding: 8px 16px; background: #2b2b2b; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; font-weight: 600; }
        .btn:hover { background: #000; }
        
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 13px; vertical-align: middle; }
        th { background: #f4f4f4; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #555; }

        .alert-error { padding: 12px 15px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 20px; font-size: 13px; border: 1px solid #f5c6cb; }
        .alert-success { padding: 12px 15px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px; font-size: 13px; border: 1px solid #c3e6cb; }

        /* Status Badge Styling */
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        /* Action Form Styling */
        .status-form { display: flex; gap: 6px; align-items: center; }
        .status-form select { padding: 5px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 12px; }
        .btn-update { padding: 6px 12px; background: #2b2b2b; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: 600; }
        .btn-update:hover { background: #000; }
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

        <?php if (isset($_SESSION['status_msg'])): ?>
            <div class="alert-<?php echo $_SESSION['status_type']; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['status_msg']); 
                    unset($_SESSION['status_msg'], $_SESSION['status_type']);
                ?>
            </div>
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
                        <th>STATUS</th>
                        <th>UPDATE ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="7" style="text-align:center;">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <?php $currentStatus = $order['status'] ?? 'Pending'; ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($order['id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['customer_name'] ?? $order['name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_email'] ?? $order['email'] ?? 'N/A'); ?></td>
                                <td>$<?php echo number_format($order['total_amount'] ?? $order['total'] ?? 0, 2); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($currentStatus); ?>">
                                        <?php echo htmlspecialchars($currentStatus); ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="admin-orders.php" method="POST" class="status-form">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status">
                                            <option value="Pending" <?php echo $currentStatus === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Completed" <?php echo $currentStatus === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="Cancelled" <?php echo $currentStatus === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn-update">Update</button>
                                    </form>
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