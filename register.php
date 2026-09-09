<?php
session_start();
require 'db.php';

// Redirect if user is already logged in
if (isset($_SESSION['user_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$name  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // 1. Validation Checks
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // 2. Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = 'An account with this email address already exists.';
            } else {
                // 3. Hash password securely (Bcrypt)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 4. Insert new user record
                $insertStmt = $pdo->prepare(
                    "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')"
                );
                $insertStmt->execute([$name, $email, $hashed_password]);

                $new_user_id = $pdo->lastInsertId();

                // 5. Regenerate Session ID & Log user in
                session_regenerate_id(true);

                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id']        = $new_user_id;
                $_SESSION['user_name']      = $name;
                $_SESSION['user_email']     = $email;

                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'A database error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Florellea</title>
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
            max-width: 420px;
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
            <h1>Create Account</h1>
            <p class="auth-sub">Join Florellea to enjoy a personalized experience</p>

            <?php if ($error): ?>
                <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required placeholder="">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required placeholder="Minimum 8 characters">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder="Repeat password">
                </div>

                <button type="submit" class="btn-auth">CREATE ACCOUNT</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Log In</a>
            </div>
        </div>
    </div>

</body>
</html>