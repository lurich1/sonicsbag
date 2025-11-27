<?php
require_once 'config.php';

$pageTitle = 'Contact Us';
$pageDescription = 'Get in touch with SONCIS. Visit us in Takoradi, Ghana or contact us via phone, email, or WhatsApp.';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-20">
    <!-- Hero Section -->
    <section class="relative h-64 overflow-hidden">
        <img src="<?php echo asset('assets/images/banner-large-image1.jpg'); ?>" alt="Contact SONCIS" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-primary/40"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white uppercase">Contact Us</h1>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">
                <!-- Contact Details -->
                <div>
                    <h2 class="text-3xl font-bold mb-6">Get in Touch</h2>
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-semibold mb-2 flex items-center gap-2">
                                <iconify-icon icon="mdi:map-marker" width="24" height="24"></iconify-icon>
                                Address
                            </h3>
                            <p class="text-muted-foreground">Takoradi, Ghana</p>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2 flex items-center gap-2">
                                <iconify-icon icon="mdi:phone" width="24" height="24"></iconify-icon>
                                Phone
                            </h3>
                            <a href="tel:+233533431086" class="text-muted-foreground hover:text-primary transition">
                                0533431086
                            </a>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2 flex items-center gap-2">
                                <iconify-icon icon="mdi:whatsapp" width="24" height="24"></iconify-icon>
                                WhatsApp
                            </h3>
                            <a href="#" class="text-muted-foreground hover:text-primary transition">
                                Chat with us
                            </a>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2 flex items-center gap-2">
                                <iconify-icon icon="mdi:email" width="24" height="24"></iconify-icon>
                                Email
                            </h3>
                            <a href="mailto:contact@soncis.com" class="text-muted-foreground hover:text-primary transition">
                                contact@soncis.com
                            </a>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2 flex items-center gap-2">
                                <iconify-icon icon="mdi:clock-outline" width="24" height="24"></iconify-icon>
                                Operating Hours
                            </h3>
                            <p class="text-muted-foreground">Monday – Saturday: 8:30 AM – 5:30 PM</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div>
                    <h2 class="text-3xl font-bold mb-6">Send us a Message</h2>
                    <form class="space-y-4" method="POST" action="/contact-handler.php" id="contact-form">
                        <div>
                            <label for="name" class="block text-sm font-medium mb-2">Name</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2">Email</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium mb-2">Subject</label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium mb-2">Message</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="5" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            ></textarea>
                        </div>
                        <button 
                            type="submit" 
                            class="w-full bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition font-semibold"
                        >
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('contact-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const button = this.querySelector('button[type="submit"]');
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Sending...';
    
    try {
        const response = await fetch('/contact-handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            this.reset();
        } else {
            alert(result.message || 'An error occurred. Please try again.');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    } finally {
        button.disabled = false;
        button.textContent = originalText;
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

