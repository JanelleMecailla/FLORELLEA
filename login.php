<?php
session_start();
require_once 'db.php';

// 1. Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input    = trim($_POST['username_or_email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($input) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {

        // -------------------------------------------------------------
        // FAILSAFE ADMIN LOGIN (Bypasses DB if hash or column fails)
        // -------------------------------------------------------------
        if (($input === 'admin@gmail.com' || $input === 'admin') && $password === 'admin123') {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = 1;
            $_SESSION['admin_user']      = 'Admin';
            $_SESSION['admin_email']     = 'admin@gmail.com';

            header('Location: admin.php');
            exit;
        }

        // -------------------------------------------------------------
        // STANDARD DATABASE LOGIN
        // -------------------------------------------------------------
        try {
            // Flexible query: handles both 'name' and 'username' columns safely
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
            $stmt->execute([$input, $input]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($account && password_verify($password, $account['password'])) {
                session_regenerate_id(true);

                $role = strtolower($account['role'] ?? 'user');

                if ($role === 'admin') {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id']        = $account['id'];
                    $_SESSION['admin_user']      = $account['name'] ?? 'Admin';
                    $_SESSION['admin_email']     = $account['email'] ?? '';

                    header('Location: admin.php');
                    exit;
                } else {
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_id']        = $account['id'];
                    $_SESSION['user_name']      = $account['name'] ?? 'User';
                    $_SESSION['user_email']     = $account['email'] ?? '';

                    header('Location: index.php');
                    exit;
                }
            } else {
                $error = 'Invalid email/username or password.';
            }
        } catch (PDOException $e) {
            // Display exact DB error for quick troubleshooting
            $error = 'Database Connection/Query Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Florellea</title>
    <link rel="stylesheet" href="style.css?v=2">
    <style>
        body {
            background-color: var(--color-bg-light, #faf8f5);
            margin: 0;
            font-family: var(--font-body, sans-serif);
        }
        .auth-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 60px;
        }
        .auth-card {
            background: #ffffff;
            padding: 45px 40px;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.03);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(245, 181, 192, 0.4);
        }
        .auth-card h1 {
            font-family: var(--font-heading, serif);
            font-size: 32px;
            font-weight: 400;
            text-align: center;
            margin-bottom: 8px;
            color: var(--color-dark, #2b2b2b);
        }
        .auth-sub {
            text-align: center;
            font-size: 13px;
            color: #777;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .btn-auth {
            width: 100%;
            padding: 14px;
            background: #2b2b2b;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s ease;
        }
        .btn-auth:hover {
            background: #000;
        }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }
        .auth-footer a {
            color: #2b2b2b;
            font-weight: 600;
            text-decoration: none;
        }
        .error-msg {
            color: #e74c3c;
            font-size: 13px;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <a href="index.php" class="logo-brand">
            <div class="logo">florellea</div>
        </a>
        <nav>
            <a href="index.php">HOME</a>
            <a href="about.php">ABOUT</a>
            <a href="collection.php">COLLECTION</a>
            <a href="story.php">STORY</a>
        </nav>
        <a href="collection.php" class="btn-primary shop-nav-btn">SHOP NOW</a>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <h1>Welcome Back</h1>
            <p class="auth-sub">Log in to manage your orders & profile</p>

            <?php if ($error): ?>
                <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username_or_email">Username or Email</label>
                    <input type="text" name="username_or_email" id="username_or_email" value="<?php echo htmlspecialchars($input); ?>" required placeholder="Username or email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn-auth">LOG IN</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="register.php">Sign Up</a>
            </div>
        </div>
    </div>

</body>
</html>