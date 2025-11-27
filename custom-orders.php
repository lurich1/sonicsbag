<?php
require_once 'config.php';

$pageTitle = 'Custom Orders';
$pageDescription = 'Order custom bags tailored to your needs.';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-20">
    <!-- Hero Section -->
    <section class="relative h-64 overflow-hidden">
        <img src="<?php echo asset('assets/images/banner-large-image1.jpg'); ?>" alt="Custom Orders" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-primary/40"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white uppercase">Custom Orders</h1>
        </div>
    </section>

    <!-- Custom Order Form -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Create Your Custom Bag</h2>
                    <p class="text-muted-foreground">
                        Tell us about your vision and we'll bring it to life. Perfect for corporate gifts, school bags, promotional items, and more.
                    </p>
                </div>

                <form class="space-y-6" method="POST" action="/custom-order-handler.php" id="custom-order-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium mb-2">Full Name *</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2">Email *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2">Phone Number *</label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="quantity" class="block text-sm font-medium mb-2">Quantity *</label>
                            <input 
                                type="number" 
                                id="quantity" 
                                name="quantity" 
                                min="1" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="bag-type" class="block text-sm font-medium mb-2">Bag Type *</label>
                        <select 
                            id="bag-type" 
                            name="bag_type" 
                            required
                            class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        >
                            <option value="">Select bag type</option>
                            <option value="school-bag">School Bag</option>
                            <option value="laptop-bag">Laptop Bag</option>
                            <option value="travel-bag">Travel Bag</option>
                            <option value="tote-bag">Tote Bag</option>
                            <option value="backpack">Backpack</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium mb-2">Description & Requirements *</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="6" 
                            required
                            placeholder="Describe your custom bag requirements, including size, color, materials, logo placement, etc."
                            class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        ></textarea>
                    </div>

                    <div>
                        <label for="budget" class="block text-sm font-medium mb-2">Budget Range</label>
                        <input 
                            type="text" 
                            id="budget" 
                            name="budget" 
                            placeholder="e.g., ₵500 - ₵1000"
                            class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-medium mb-2">Desired Completion Date</label>
                        <input 
                            type="date" 
                            id="deadline" 
                            name="deadline" 
                            class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition font-semibold uppercase"
                    >
                        Submit Custom Order Request
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('custom-order-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const button = this.querySelector('button[type="submit"]');
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Submitting...';
    
    try {
        const response = await fetch('/custom-order-handler.php', {
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

