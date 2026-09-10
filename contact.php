<?php
session_start();

// Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (file_exists('db.php')) {
    require 'db.php';
}
if (file_exists('auth-check.php')) {
    require 'auth-check.php';
}

// Auto-fill user info if logged in
$logged_name = $_SESSION['user_name'] ?? '';
$logged_email = $_SESSION['user_email'] ?? '';

$success = false;
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            if (isset($pdo)) {
                $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, is_read) VALUES (?, ?, ?, 0)");
                $stmt->execute([$name, $email, $message]);
                $success = true;
            } else {
                $error_msg = "Database connection unavailable.";
            }
        } catch (PDOException $e) {
            $error_msg = "Database error: " . $e->getMessage();
        }
    } else {
        $error_msg = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Florellea</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #fcf8f6;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- Header Navigation --- */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 80px;
            background-color: #fcf8f6;
        }

        .logo {
            font-family: serif;
            font-size: 28px;
            color: #2b2b2b;
            text-decoration: none;
            letter-spacing: 0.5px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #555;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .btn-shop {
            background-color: #f7b2bd;
            color: #7d2e3d;
            padding: 10px 24px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: opacity 0.2s;
        }

        .btn-shop:hover {
            opacity: 0.9;
        }

        /* --- Main Content Section --- */
        .main-container {
            max-width: 1100px;
            margin: 40px auto 80px auto;
            padding: 0 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            flex-grow: 1;
        }

        .section-label {
            font-size: 11px;
            letter-spacing: 2px;
            color: #e5989b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .section-title {
            font-family: serif;
            font-size: 32px;
            font-weight: normal;
            color: #2c2c2c;
            margin-bottom: 30px;
        }

        /* --- Form Styles --- */
        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #e2d9d5;
            border-radius: 6px;
            background-color: #ffffff;
            font-size: 14px;
            color: #333;
            outline: none;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .form-control:focus {
            border-color: #f7b2bd;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background-color: #f7b2bd;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #f59eb0;
        }

        /* --- Contact Details --- */
        .info-block {
            margin-bottom: 25px;
        }

        .info-label {
            font-size: 11px;
            letter-spacing: 1.5px;
            color: #f7b2bd;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 14px;
            color: #444;
            line-height: 1.5;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        /* --- Footer --- */
        .footer {
            background-color: #4a4a4a;
            color: #d1d1d1;
            padding: 60px 80px 30px 80px;
            margin-top: auto;
        }

        .footer-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 2fr;
            gap: 60px;
            padding-bottom: 40px;
            border-bottom: 1px solid #5a5a5a;
        }

        .footer-brand {
            font-family: serif;
            font-size: 22px;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .footer-desc {
            font-size: 12px;
            line-height: 1.6;
            color: #b0b0b0;
            max-width: 280px;
        }

        .footer-heading {
            font-size: 11px;
            letter-spacing: 1.5px;
            color: #d8a274;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 8px;
        }

        .footer-links a {
            color: #cccccc;
            text-decoration: none;
            font-size: 12px;
        }

        .newsletter-form {
            display: flex;
            margin-top: 15px;
        }

        .newsletter-input {
            padding: 8px 12px;
            border: none;
            border-radius: 2px 0 0 2px;
            font-size: 12px;
            width: 70%;
            outline: none;
        }

        .newsletter-btn {
            background-color: #d8a274;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            border-radius: 0 2px 2px 0;
            cursor: pointer;
            text-transform: uppercase;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 25px;
            font-size: 11px;
            color: #888888;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="navbar">
        <a href="index.php" class="logo">florellea</a>
        <ul class="nav-links">
            <li><a href="index.php">HOME</a></li>
            <li><a href="about.php">ABOUT</a></li>
            <li><a href="collection.php">COLLECTION</a></li>
            <li><a href="story.php">STORY</a></li>
            <li><a href="contact.php" style="color: #000;">CONTACT</a></li>
            <li><a href="shop.php" class="btn-shop">SHOP NOW</a></li>
        </ul>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        
        <!-- Left: Form Column -->
        <div class="form-column">
            <div class="section-label">SEND A MESSAGE</div>
            <h1 class="section-title">We'd Love to Hear From You</h1>

            <?php if ($success): ?>
                <div class="alert-success">Thank you! Your message has been sent successfully.</div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <form method="POST" action="contact.php">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" value="<?php echo htmlspecialchars($logged_name); ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Your Email" value="<?php echo htmlspecialchars($logged_email); ?>" required>
                </div>
                <div class="form-group">
                    <textarea name="message" class="form-control" placeholder="Your Message" required></textarea>
                </div>
                <button type="submit" class="btn-submit">SEND MESSAGE</button>
            </form>
        </div>

        <!-- Right: Contact Info Column -->
        <div class="info-column">
            <div class="section-label">CONTACT DETAILS</div>
            <h1 class="section-title">Visit or Reach Us</h1>

            <div class="info-block">
                <div class="info-label">EMAIL</div>
                <div class="info-value">hello@florellea.com</div>
            </div>

            <div class="info-block">
                <div class="info-label">PHONE</div>
                <div class="info-value">+63 912 345 6789</div>
            </div>

            <div class="info-block">
                <div class="info-label">ADDRESS</div>
                <div class="info-value">Siquijor, Philippines</div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">florellea</div>
                <p class="footer-desc">Crafting elegant, artisanal fragrances inspired by nature's finest blooms and botanical elements.</p>
            </div>
            <div>
                <div class="footer-heading">NAVIGATION</div>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="collection.php">Collection</a></li>
                    <li><a href="cart.php">Shopping Bag</a></li>
                </ul>
            </div>
           
        <div class="footer-bottom">
            &copy; 2026 Florellea Fragrances. All rights reserved.
        </div>
    </footer>

</body>
</html>