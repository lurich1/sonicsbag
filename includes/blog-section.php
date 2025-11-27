<?php
$blogPosts = [
    [
        'id' => 1,
        'title' => 'How to look outstanding in pastel',
        'category' => 'Fashion',
        'date' => 'Jul 11, 2022',
        'image' => 'assets/images/post-image1.jpg',
        'excerpt' => 'Dignissim lacus, turpis ut suspendisse vel tellus. Turpis purus, gravida orci, fringilla...',
    ],
    [
        'id' => 2,
        'title' => 'Top 10 fashion trend for summer',
        'category' => 'Fashion',
        'date' => 'Jul 11, 2022',
        'image' => 'assets/images/post-image2.jpg',
        'excerpt' => 'Turpis purus, gravida orci, fringilla dignissim lacus, turpis ut suspendisse vel tellus...',
    ],
    [
        'id' => 3,
        'title' => 'Crazy fashion with unique moment',
        'category' => 'Lifestyle',
        'date' => 'Jul 11, 2022',
        'image' => 'assets/images/post-image3.jpg',
        'excerpt' => 'Turpis purus, gravida orci, fringilla dignissim lacus, turpis ut suspendisse vel tellus...',
    ],
];
?>

<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-12">
            <h2 class="text-3xl font-bold uppercase tracking-wide">Read Blog Posts</h2>
            <a href="#" class="text-primary font-semibold uppercase text-sm hover:underline">
                View All →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($blogPosts as $post): ?>
                <article class="group">
                    <div class="relative h-64 mb-4 overflow-hidden rounded-lg bg-muted">
                        <img
                            src="<?php echo asset($post['image']); ?>"
                            alt="<?php echo htmlspecialchars($post['title']); ?>"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                        />
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="text-xs uppercase tracking-wide text-muted-foreground">
                            <?php echo htmlspecialchars($post['category']); ?> • <?php echo htmlspecialchars($post['date']); ?>
                        </div>
                        <h3 class="text-lg font-semibold uppercase hover:text-primary transition-colors">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h3>
                        <p class="text-muted-foreground text-sm leading-relaxed">
                            <?php echo htmlspecialchars($post['excerpt']); ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

