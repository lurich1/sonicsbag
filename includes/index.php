<?php
require_once 'config.php';

$pageTitle = 'Home';
$pageDescription = 'If it needs a bag, it needs SONCIS. Crafted with Purpose. Built to Last. Quality custom bags that carry impact - from classrooms to travel adventures.';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "SONCIS",
        "description": "If it needs a bag, it needs SONCIS. Crafted with Purpose. Built to Last.",
        "url": "<?php echo SITE_URL; ?>",
        "logo": "<?php echo SITE_URL; ?>/assets/images/photo_2025-11-02_05-35-41-removebg-preview.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "customer service"
        }
    }
    </script>

    <?php include INCLUDES_PATH . '/hero-banner.php'; ?>
    <?php include INCLUDES_PATH . '/features-grid.php'; ?>
    <?php include INCLUDES_PATH . '/product-grid.php'; ?>
    <?php include INCLUDES_PATH . '/testimonials-carousel.php'; ?>
    <?php include INCLUDES_PATH . '/blog-section.php'; ?>
    <?php include INCLUDES_PATH . '/newsletter-section.php'; ?>
    <?php include INCLUDES_PATH . '/instagram-section.php'; ?>
</main>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

