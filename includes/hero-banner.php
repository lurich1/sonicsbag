<?php
$slides = [
    [
        'id' => 1,
        'image' => asset('assets/images/banner-large-image3.jpg'),
        'title' => 'Timeless Elegance',
        'description' => 'Sed condimentum ipsum, ultrices in aliquam ac hendrerit diam praesent. Ac dui convallis elit odio eget a commodo.',
    ],
    [
        'id' => 2,
        'image' => asset('assets/images/banner-large-image2.jpg'),
        'title' => 'Unmatched Craftsmanship',
        'description' => 'Sed condimentum ipsum, ultrices in aliquam ac hendrerit diam praesent. Ac dui convallis elit odio eget a commodo.',
    ],
    [
        'id' => 3,
        'image' => asset('assets/images/banner-large-image1.jpg'),
        'title' => '100% Genuine Leather',
        'description' => 'Sed condimentum ipsum, ultrices in aliquam ac hendrerit diam praesent. Ac dui convallis elit odio eget a commodo.',
    ],
];
?>
<section class="relative w-full h-screen -mt-20 overflow-hidden" id="hero-banner">
    <?php foreach ($slides as $index => $slide): ?>
        <div class="hero-slide absolute inset-0 transition-opacity duration-1000 <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>" data-slide="<?php echo $index; ?>">
            <img 
                src="<?php echo $slide['image']; ?>" 
                alt="<?php echo htmlspecialchars($slide['title']); ?>"
                class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center text-white max-w-3xl px-4">
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 text-balance"><?php echo htmlspecialchars($slide['title']); ?></h1>
                    <p class="text-xl md:text-2xl mb-8"><?php echo htmlspecialchars($slide['description']); ?></p>
                    <a href="<?php echo url('shop.php'); ?>" class="inline-block bg-accent text-accent-foreground px-8 py-3 rounded hover:opacity-90 transition font-semibold uppercase">
                        Shop Collection
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Navigation Dots -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-3 z-10">
        <?php foreach ($slides as $index => $slide): ?>
            <button
                onclick="goToSlide(<?php echo $index; ?>)"
                class="hero-dot w-3 h-3 rounded-full transition <?php echo $index === 0 ? 'bg-white' : 'bg-white/50'; ?>"
                aria-label="Go to slide <?php echo $index + 1; ?>"
                data-dot="<?php echo $index; ?>"
            ></button>
        <?php endforeach; ?>
    </div>
</section>

<script>
let currentSlide = 0;
const totalSlides = <?php echo count($slides); ?>;

function goToSlide(index) {
    currentSlide = index;
    updateSlides();
}

function updateSlides() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    
    slides.forEach((slide, index) => {
        if (index === currentSlide) {
            slide.classList.remove('opacity-0');
            slide.classList.add('opacity-100');
        } else {
            slide.classList.remove('opacity-100');
            slide.classList.add('opacity-0');
        }
    });
    
    dots.forEach((dot, index) => {
        if (index === currentSlide) {
            dot.classList.remove('bg-white/50');
            dot.classList.add('bg-white');
        } else {
            dot.classList.remove('bg-white');
            dot.classList.add('bg-white/50');
        }
    });
}

// Auto-advance slides
setInterval(() => {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateSlides();
}, 5000);
</script>

