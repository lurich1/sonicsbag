<?php
$instaImages = [
    ['id' => 1, 'image' => 'assets/images/insta-item1.jpg'],
    ['id' => 2, 'image' => 'assets/images/insta-item2.jpg'],
    ['id' => 3, 'image' => 'assets/images/insta-item3.jpg'],
];
?>

<section class="py-12 bg-primary">
    <div class="container mx-auto px-4">
        <h3 class="text-center text-primary-foreground mb-8 uppercase tracking-wide">
            Follow us on Instagram
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($instaImages as $item): ?>
                <a
                    href="https://www.instagram.com/templatesjungle/"
                    target="_blank"
                    rel="noreferrer"
                    class="relative h-48 md:h-64 overflow-hidden rounded-lg group"
                >
                    <img
                        src="<?php echo asset($item['image']); ?>"
                        alt="Instagram preview"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        loading="lazy"
                    />
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

