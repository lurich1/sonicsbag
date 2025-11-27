<?php
$features = [
    [
        'icon' => 'mdi:heart-outline',
        'title' => '100% Genuine Leather',
        'description' => 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.',
    ],
    [
        'icon' => 'mdi:package-variant',
        'title' => 'Hand-Stitched Perfection',
        'description' => 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.',
    ],
    [
        'icon' => 'mdi:chat-outline',
        'title' => 'Minimalist',
        'description' => 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.',
    ],
    [
        'icon' => 'mdi:flash',
        'title' => 'Functional Designs',
        'description' => 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.',
    ],
    [
        'icon' => 'mdi:earth',
        'title' => 'Ethically Crafted',
        'description' => 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.',
    ],
    [
        'icon' => 'mdi:leaf',
        'title' => 'Free Worldwide Shipping',
        'description' => 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.',
    ],
    [
        'icon' => 'mdi:backup-restore',
        'title' => 'Easy Returns',
        'description' => 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.',
    ],
    [
        'icon' => 'mdi:sparkles',
        'title' => 'Sustainable',
        'description' => 'At imperdiet dui accumsan sit amet nulla risus est ultricies quis.',
    ],
];
?>
<section class="py-10 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($features as $feature): ?>
                <div class="text-foreground">
                    <div class="py-4 md:py-6">
                        <iconify-icon icon="<?php echo htmlspecialchars($feature['icon']); ?>" class="w-12 h-12 md:w-[75px] md:h-[75px] mb-2 md:mb-3 text-accent"></iconify-icon>
                        <h4 class="text-foreground font-semibold capitalize my-2 md:my-3 text-sm md:text-base"><?php echo htmlspecialchars($feature['title']); ?></h4>
                        <p class="text-muted-foreground text-xs md:text-sm leading-relaxed"><?php echo htmlspecialchars($feature['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

