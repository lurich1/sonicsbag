<section class="relative py-20 overflow-hidden">
    <div
        class="absolute inset-0 -z-10 bg-center bg-cover"
        style="background-image: url('<?php echo asset('assets/images/bg-newsletter.jpg'); ?>');"
        aria-hidden="true"
    ></div>
    <div class="absolute inset-0 bg-black/40 -z-10"></div>

    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center text-white">
            <h2 class="text-4xl font-bold mb-8 uppercase tracking-wide">Sign Up for Our Newsletter</h2>
            <form id="newsletter-form" class="flex flex-col sm:flex-row gap-4 justify-center">
                <input
                    type="email"
                    name="email"
                    placeholder="Your Email Address"
                    class="flex-1 px-4 py-3 rounded text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent"
                    required
                />
                <button
                    type="submit"
                    class="bg-accent text-accent-foreground px-8 py-3 rounded font-semibold uppercase hover:opacity-90 transition"
                >
                    Sign Up
                </button>
            </form>
            <p id="newsletter-message" class="text-sm mt-4"></p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('newsletter-form');
    const messageEl = document.getElementById('newsletter-message');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        messageEl.textContent = 'Submitting...';

        try {
            const response = await fetch('<?php echo url('newsletter-handler.php'); ?>', {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();
            if (result.success) {
                form.reset();
                messageEl.textContent = 'Thank you! You are now subscribed.';
                messageEl.classList.add('text-green-200');
                messageEl.classList.remove('text-red-200');
            } else {
                throw new Error(result.message || 'Something went wrong');
            }
        } catch (error) {
            messageEl.textContent = error.message || 'Subscription failed. Please try again.';
            messageEl.classList.add('text-red-200');
            messageEl.classList.remove('text-green-200');
        }
    });
});
</script>

