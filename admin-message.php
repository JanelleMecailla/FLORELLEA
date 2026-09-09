<?php
session_start();
require 'db.php';

// Mark message as read
if (isset($_GET['read_id'])) {
    $read_id = (int)$_GET['read_id'];
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$read_id]);
    header("Location: admin-messages.php");
    exit();
}

// Fetch all messages
$stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Messages</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background: #fafafa; }
        .container { max-width: 950px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #eee; padding: 12px; text-align: left; }
        th { background-color: #f3a6b8; color: white; }
        .unread { background-color: #fff0f3; font-weight: bold; }
        .btn-read { background: #4CAF50; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Dashboard - Messages</h2>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Sender Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $row): ?>
                    <tr class="<?php echo $row['is_read'] == 0 ? 'unread' : ''; ?>">
                        <td><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                        <td><?php echo $row['is_read'] == 0 ? '🔴 New' : '🟢 Read'; ?></td>
                        <td>
                            <?php if ($row['is_read'] == 0): ?>
                                <a href="admin-messages.php?read_id=<?php echo $row['id']; ?>" class="btn-read">Mark Read</a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">No messages received yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>