<?php require 'auth-check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Florellea - Essence of Elegance</title>
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

    <!-- NAVIGATION -->
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
        <div class="nav-auth">
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <span style="font-size: 13px; margin-right: 15px;">
                    Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                </span>
                <a href="user-logout.php" class="btn-primary">LOGOUT</a>
            <?php else: ?>
                <a href="login.php" class="btn-primary">LOGIN</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section id="home" class="hero">
        <div class="hero-container">
            <p class="tagline">ESSENCE OF ELEGANCE</p>
            <h1 class="brand-title">florellea</h1>
            <p class="hero-description">Where floral dreams<br>become your signature.</p>
            <a href="collection.php" class="btn-hero" style="display: inline-block; text-decoration: none; text-align: center;">DISCOVER NOW</a>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="about">
        <div class="about-container">
            <div class="about-image-wrapper">
                <div class="img-placeholder arch-placeholder">
                    <span>about.png<br><small>450 x 550</small></span>
                    <img src="images/about.png" alt="Florellea bottle" onerror="this.style.display='none'">
                </div>
            </div>
            <div class="about-content">
                <p class="section-label">ABOUT FLORELLEA</p>
                <h2>Inspired by Nature,<br>Made for You</h2>
                <p class="description">
                    Florellea is a perfume brand that captures the beauty of nature in every bottle. 
                    Each scent is thoughtfully crafted to inspire confidence, elegance, and unforgettable moments.
                </p>
                <a href="about.php" class="btn-about" style="display: inline-block; text-decoration: none; text-align: center;">LEARN MORE</a>
            </div>
        </div>
    </section>

    <!-- COLLECTION SECTION -->
    <section id="collection" class="collection">
        <p class="section-label">OUR COLLECTION</p>
        <h2>Scents for Every Mood</h2>

        <div class="products-grid">
            <!-- Product 1 -->
            <div class="product-card">
                <div class="arch-frame brand-pink-bg">
                    <div class="img-placeholder">
                        <span>blush-bloom.jpg</span>
                        <img src="images/blush-bloom.jpg" alt="Blush Bloom" onerror="this.style.display='none'">
                    </div>
                </div>
                <div class="brand-sub"></div>
                <h3>Blush Bloom</h3>
                <p class="notes">Floral · Soft · Romantic</p>
                <span class="price">$29</span>
            </div>

            <!-- Product 2 -->
            <div class="product-card">
                <div class="arch-frame brand-green-bg">
                    <div class="img-placeholder">
                        <span>garden-whisper.jpg</span>
                        <img src="images/garden-whisper.jpg" alt="Garden Whisper" onerror="this.style.display='none'">
                    </div>
                </div>
                <div class="brand-sub"></div>
                <h3>Garden Whisper</h3>
                <p class="notes">Fresh · Green · Calm</p>
                <span class="price">$26</span>
            </div>

            <!-- Product 3 -->
            <div class="product-card">
                <div class="arch-frame brand-pink-bg">
                    <div class="img-placeholder">
                        <span>petal-muse.jpg</span>
                        <img src="images/petal-muse.jpg" alt="Petal Muse" onerror="this.style.display='none'">
                    </div>
                </div>
                <div class="brand-sub"></div>
                <h3>Petal Muse</h3>
                <p class="notes">Sweet · Elegant · Feminine</p>
                <span class="price">$23</span>
            </div>

            <!-- Product 4 -->
            <div class="product-card">
                <div class="arch-frame brand-green-bg">
                    <div class="img-placeholder">
                        <span>golden-aura.jpg</span>
                        <img src="images/golden-aura.jpg" alt="Golden Aura" onerror="this.style.display='none'">
                    </div>
                </div>
                <div class="brand-sub"></div>
                <h3>Golden Aura</h3>
                <p class="notes">Warm · Sweet · Luxurious</p>
                <span class="price">$32</span>
            </div>
        </div>

        <a href="collection.php" class="btn-collection" style="display: inline-block; text-decoration: none; text-align: center;">VIEW ALL COLLECTIONS</a>
    </section>

    <!-- STORY SECTION -->
    <section id="story" class="story">
        <div class="story-container">
            <div class="story-image-wrapper">
                <div class="img-placeholder arch-placeholder">
                    <span>story.png<br><small>450 x 550</small></span>
                    <img src="images/story.jpg" alt="Our Story" onerror="this.style.display='none'">
                </div>
            </div>

            <div class="story-content">
                <p class="section-label">OUR STORY</p>
                <h2>A Bloom That<br>Tells Your Story</h2>
                <p>Every bottle of Florellea is more than just a scent—it's a memory, a feeling, and a part of you.</p>
                <p>We believe in the power of fragrance to express who you are.</p>
                <a href="story.php" class="btn-story" style="display: inline-block; text-decoration: none; text-align: center;">DISCOVER OUR STORY</a>
            </div>
        </div>
    </section>

    <!-- FOOTER & CONTACT -->
    <footer id="contact">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="logo">florellea</div>
                <p class="footer-tagline">STAY CONNECTED</p>
            </div>

            <div class="newsletter">
                <h3>Be the first to know</h3>
                <p>about new scents and special offers.</p>
                
                <!-- SUCCESS / ERROR MESSAGE DISPLAY -->
                <div id="subscribe-message" style="font-size: 13px; margin-top: 8px; font-weight: 500;"></div>

                <form id="subscribe-form" class="subscribe-form" action="subscribe.php" method="POST">
                    <input type="email" id="subscribe-email" name="email" placeholder="Enter your email" required>
                    <button type="submit" id="subscribe-btn" class="btn-contact">SUBSCRIBE</button>
                </form>
            </div>

            <div class="contact-info">
                <h3>CONTACT US</h3>
                <p>hello@florellea.com</p>
                <p>+63 912 345 6789</p>
                <p>Siquijor, Philippines</p>
            </div>
        </div>

        <div class="copyright">
            <p>©2026 Florellea. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">PRIVACY POLICY</a>
                <a href="#">TERMS & CONDITIONS</a>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT FOR RESPONSIVE NEWSLETTER SUBMISSION -->
    <script>
    document.getElementById('subscribe-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Stop standard form refresh

        const emailInput = document.getElementById('subscribe-email');
        const submitBtn = document.getElementById('subscribe-btn');
        const messageDiv = document.getElementById('subscribe-message');

        const email = emailInput.value.trim();

        if (!email) return;

        // UI Feedback: Disable button during processing
        submitBtn.disabled = true;
        submitBtn.textContent = 'SENDING...';
        messageDiv.textContent = '';

        fetch('subscribe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                messageDiv.style.color = '#7dce94'; // Success green
                messageDiv.textContent = data.message;
                emailInput.value = ''; // Clear input field
            } else {
                messageDiv.style.color = '#f87171'; // Error red
                messageDiv.textContent = data.message;
            }
        })
        .catch(error => {
            messageDiv.style.color = '#f87171';
            messageDiv.textContent = 'Unable to subscribe. Please try again.';
        })
        .finally(() => {
            submitBtn.disabled = true;
            submitBtn.textContent = 'SUBSCRIBE';
        });
    });
    </script>

</body>
</html>