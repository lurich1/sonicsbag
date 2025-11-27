<?php
require_once 'config.php';

$pageTitle = 'About Us';
$pageDescription = 'Learn about SONCIS - our mission, vision, and the story behind our premium leather goods.';

$coreValues = [
    ['letter' => 'I', 'word' => 'Integrity', 'description' => 'We operate with honesty and transparency in all our dealings.'],
    ['letter' => 'N', 'word' => 'Nurturing Community', 'description' => 'We build relationships and support the communities we serve.'],
    ['letter' => 'S', 'word' => 'Sustainability', 'description' => 'We are committed to environmental responsibility and sustainable practices.'],
    ['letter' => 'P', 'word' => 'Pursuit of Excellence', 'description' => 'We strive for the highest quality in everything we create.'],
    ['letter' => 'I', 'word' => 'Innovation', 'description' => 'We continuously improve and innovate our designs and processes.'],
    ['letter' => 'R', 'word' => 'Responsibility', 'description' => 'We take responsibility for our impact on people and the planet.'],
    ['letter' => 'E', 'word' => 'Empowerment', 'description' => 'We empower learners, professionals, and communities through our work.'],
];

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background">
    <!-- Hero Banner -->
    <section class="relative h-96 -mt-20 overflow-hidden">
        <img src="<?php echo asset('assets/images/banner-large-image1.jpg'); ?>" alt="About SONCIS" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-primary/40"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-5xl md:text-6xl font-bold text-white uppercase">About Us</h1>
        </div>
    </section>

    <!-- Our Story -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold mb-8 text-center">Our Story: From Purpose to Production</h2>
                <div class="prose prose-lg mx-auto">
                    <p class="text-lg text-muted-foreground leading-relaxed mb-6">
                        SONCIS began with a simple idea — to create an income stream that supports the non-profit Once a Child. 
                        What started as small handmade bags evolved into a brand that blends creativity, craftsmanship, and community impact.
                    </p>
                    <p class="text-lg text-muted-foreground leading-relaxed mb-6">
                        The name SONCIS combines the last three letters of our founder's surname and first name — Francis Quayson — 
                        representing personal dedication and innovation.
                    </p>
                    <p class="text-lg text-muted-foreground leading-relaxed">
                        Today, SONCIS produces a range of high-quality bags — from educational and travel bags to corporate and promotional designs — 
                        all made locally in Ghana.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-16 bg-muted">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">
                <!-- Mission -->
                <div class="bg-card p-8 rounded-lg border border-border">
                    <h3 class="text-3xl font-bold mb-4">Our Mission</h3>
                    <p class="text-muted-foreground leading-relaxed">
                        At SONCIS, we craft high-quality and affordable bags for every stage of life—school, work, travel, and beyond. 
                        We combine creativity with functionality, sustainability with style, and community impact with global standards. 
                        Our mission is to empower learners, professionals, and travelers alike, while uplifting communities and protecting the environment.
                    </p>
                </div>

                <!-- Vision -->
                <div class="bg-card p-8 rounded-lg border border-border">
                    <h3 class="text-3xl font-bold mb-4">Our Vision</h3>
                    <p class="text-muted-foreground leading-relaxed">
                        To be a globally recognized bag brand that inspires learning, travel, and everyday living through durable, innovative, 
                        and sustainable designs.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values - INSPIRE -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Our Core Values — INSPIRE</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                <?php foreach ($coreValues as $value): ?>
                    <div class="bg-card p-6 rounded-lg border border-border">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-primary"><?php echo $value['letter']; ?></span>
                            </div>
                            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($value['word']); ?></h3>
                        </div>
                        <p class="text-muted-foreground text-sm"><?php echo htmlspecialchars($value['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Quote Section -->
    <section class="py-16 bg-primary/10">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <p class="text-2xl md:text-3xl font-semibold italic mb-4 text-foreground">
                    "We don't just make bags — we carry hope, purpose, and impact."
                </p>
                <p class="text-lg text-muted-foreground">
                    — Francis Quayson, Founder
                </p>
            </div>
        </div>
    </section>

    <!-- Image Gallery Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="relative h-96 rounded-lg overflow-hidden">
                    <img src="<?php echo asset('assets/images/banner-large-image2.jpg'); ?>" alt="SONCIS Workshop" class="w-full h-full object-cover" />
                </div>
                <div class="relative h-96 rounded-lg overflow-hidden">
                    <img src="<?php echo asset('assets/images/banner-large-image3.jpg'); ?>" alt="SONCIS Production" class="w-full h-full object-cover" />
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-4">Subscribe to Our Newsletter</h2>
                <p class="text-muted-foreground mb-6">Get updates on new products and exclusive offers.</p>
                <form class="flex gap-4 max-w-md mx-auto">
                    <input 
                        type="email" 
                        placeholder="Enter your email" 
                        class="flex-1 px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        required
                    />
                    <button type="submit" class="bg-primary text-primary-foreground px-6 py-2 rounded-md hover:opacity-90 transition">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

