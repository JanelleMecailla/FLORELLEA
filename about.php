<?php require 'auth-check.php'; ?>
<?php $pageTitle = 'About'; include 'header.php'; ?>

    <!-- PAGE HERO -->
    <section class="page-hero">
        <div class="page-hero-container">
            <p class="tagline">ABOUT FLORELLEA</p>
            <h1 class="page-title">Our Essence</h1>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="about about-page">
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
                <p class="description">
                    Founded on a love for the outdoors and a passion for craftsmanship, Florellea blends rare botanicals
                    with modern perfumery to create fragrances that feel timeless yet personal. Every note is chosen
                    with intention, so each scent blooms a little differently on everyone who wears it.
                </p>
                <a class="btn-about" href="collection.php">SHOP THE COLLECTION</a>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>