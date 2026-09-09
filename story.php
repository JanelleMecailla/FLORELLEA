<?php require 'auth-check.php'; ?>
<?php $pageTitle = 'Our Story'; include 'header.php'; ?>

    <!-- PAGE HERO -->
    <section class="page-hero">
        <div class="page-hero-container">
            <p class="tagline">OUR STORY</p>
            <h1 class="page-title">A Bloom That Tells Your Story</h1>
        </div>
    </section>

    <!-- STORY SECTION -->
    <section id="story" class="story story-page">
        <div class="story-container">
            <div class="story-image-wrapper">
                <div class="img-placeholder arch-placeholder">
                    <span>story.png<br><small>450 x 550</small></span>
                    <img src="images/story.jpg" alt="Our Story" onerror="this.style.display='none'">
                </div>
            </div>

            <div class="story-content">
                <p class="section-label">HOW IT STARTED</p>
                <h2>A Bloom That<br>Tells Your Story</h2>
                <p>Every bottle of Florellea is more than just a scent—it's a memory, a feeling, and a part of you.</p>
                <p>We believe in the power of fragrance to express who you are. What began as a small collection of
                    hand-blended scents has grown into a brand devoted to capturing fleeting, beautiful moments in a bottle.</p>
                <p>From the first sketch of a label to the final drop of a fragrance, every step of the Florellea
                    journey is guided by the same idea: your scent should feel unmistakably yours.</p>
                <a class="btn-story" href="about.php">MORE ABOUT US</a>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>