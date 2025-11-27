<?php
require_once 'config.php';

$pageTitle = 'Our Impact';
$pageDescription = 'Learn about SONCIS impact on communities and the environment.';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-20">
    <!-- Hero Section -->
    <section class="relative h-64 overflow-hidden">
        <img src="<?php echo asset('assets/images/banner-large-image1.jpg'); ?>" alt="Our Impact" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-primary/40"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white uppercase">Our Impact</h1>
        </div>
    </section>

    <!-- Impact Content -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold mb-4">Making a Difference</h2>
                    <p class="text-lg text-muted-foreground">
                        Every purchase supports our mission to create positive change in communities and the environment.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-primary mb-2">100+</div>
                        <p class="text-muted-foreground">Bags Produced</p>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold text-primary mb-2">50+</div>
                        <p class="text-muted-foreground">Happy Customers</p>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold text-primary mb-2">10+</div>
                        <p class="text-muted-foreground">Communities Supported</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-card p-8 rounded-lg border border-border">
                        <h3 class="text-2xl font-bold mb-4">Supporting Once a Child</h3>
                        <p class="text-muted-foreground leading-relaxed">
                            SONCIS was founded to create an income stream that supports the non-profit organization Once a Child. 
                            A portion of every sale goes directly to supporting educational initiatives and community development programs.
                        </p>
                    </div>

                    <div class="bg-card p-8 rounded-lg border border-border">
                        <h3 class="text-2xl font-bold mb-4">Local Production</h3>
                        <p class="text-muted-foreground leading-relaxed">
                            All our bags are made locally in Ghana, supporting local artisans and creating employment opportunities 
                            in our community. We believe in fair wages and ethical production practices.
                        </p>
                    </div>

                    <div class="bg-card p-8 rounded-lg border border-border">
                        <h3 class="text-2xl font-bold mb-4">Sustainable Practices</h3>
                        <p class="text-muted-foreground leading-relaxed">
                            We are committed to environmental responsibility. Our production processes prioritize sustainability, 
                            and we continuously work to reduce our environmental footprint while maintaining the highest quality standards.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

