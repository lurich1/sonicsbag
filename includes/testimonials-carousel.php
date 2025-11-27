<?php
$testimonials = [
    [
        'id' => 1,
        'quote' => 'More than expected crazy soft, flexible and best fitted white simple denim shirt.',
        'author' => 'Casual Way',
    ],
    [
        'id' => 2,
        'quote' => 'Best fitted white denim shirt more than expected crazy soft, flexible.',
        'author' => 'Uptop',
    ],
    [
        'id' => 3,
        'quote' => 'Denim shirt that stays premium and comfortable all day.',
        'author' => 'Denim Craze',
    ],
];
?>

<section class="py-16 bg-primary text-primary-foreground overflow-hidden">
    <div class="container mx-auto px-4">
        <h2 class="text-center text-3xl font-bold mb-12 uppercase tracking-wide">Reviews</h2>

        <div class="max-w-3xl mx-auto relative">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <div
                    class="testimonial-slide text-center transition-opacity duration-500 <?php echo $index === 0 ? 'opacity-100 relative' : 'opacity-0 absolute inset-0'; ?>"
                    data-index="<?php echo $index; ?>"
                >
                    <p class="text-xl md:text-2xl mb-4 italic leading-relaxed">
                        “<?php echo htmlspecialchars($testimonial['quote']); ?>”
                    </p>
                    <p class="text-sm uppercase font-semibold tracking-wide">
                        <?php echo htmlspecialchars($testimonial['author']); ?>
                    </p>
                </div>
            <?php endforeach; ?>

            <div class="flex justify-center gap-3 mt-10">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <button
                        type="button"
                        class="testimonial-dot w-3 h-3 rounded-full transition <?php echo $index === 0 ? 'bg-primary-foreground' : 'bg-primary-foreground/50'; ?>"
                        data-target="<?php echo $index; ?>"
                        aria-label="Go to testimonial <?php echo $index + 1; ?>"
                    ></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const slides = Array.from(document.querySelectorAll('.testimonial-slide'));
    const dots = Array.from(document.querySelectorAll('.testimonial-dot'));
    if (!slides.length) return;

    let current = 0;
    let intervalId;

    const updateSlides = (nextIndex) => {
        slides.forEach((slide, idx) => {
            slide.style.opacity = idx === nextIndex ? '1' : '0';
            slide.style.position = idx === nextIndex ? 'relative' : 'absolute';
        });

        dots.forEach((dot, idx) => {
            dot.classList.toggle('bg-primary-foreground', idx === nextIndex);
            dot.classList.toggle('bg-primary-foreground/50', idx !== nextIndex);
        });

        current = nextIndex;
    };

    const startInterval = () => {
        intervalId = setInterval(() => {
            const next = (current + 1) % slides.length;
            updateSlides(next);
        }, 5000);
    };

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const target = Number(dot.dataset.target);
            if (Number.isNaN(target)) return;
            updateSlides(target);
            clearInterval(intervalId);
            startInterval();
        });
    });

    startInterval();
});
</script>

