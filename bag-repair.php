<?php
require_once 'config.php';

$pageTitle = 'Bag Repair Service';
$pageDescription = 'Request professional SONCIS bag repair, refurbishment, and restoration.';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-20">
    <!-- Hero Section -->
    <section class="relative h-64 overflow-hidden">
        <img src="<?php echo asset('assets/images/banner-large-image3.jpg'); ?>" alt="Bag Repair" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-primary/40"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white uppercase">Bag Repair</h1>
        </div>
    </section>

    <!-- Repair Form -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-12 space-y-4">
                    <h2 class="text-3xl font-bold">Submit a Repair Request</h2>
                    <p class="text-muted-foreground leading-relaxed">
                        Share the details of your SONCIS bag repair. We’ll review your request, send a quote, and coordinate pickup or drop-off.
                    </p>
                </div>

                <form class="space-y-8" method="POST" action="<?php echo url('repair-order-handler.php'); ?>" enctype="multipart/form-data" id="repair-order-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium mb-2">Full Name *</label>
                            <input 
                                type="text" 
                                id="customer_name" 
                                name="customer_name" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="customer_email" class="block text-sm font-medium mb-2">Email Address *</label>
                            <input 
                                type="email" 
                                id="customer_email" 
                                name="customer_email" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium mb-2">Phone Number *</label>
                            <input 
                                type="tel" 
                                id="customer_phone" 
                                name="customer_phone" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="address_street" class="block text-sm font-medium mb-2">Street Address *</label>
                            <input 
                                type="text" 
                                id="address_street" 
                                name="address_street" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="address_city" class="block text-sm font-medium mb-2">City *</label>
                            <input 
                                type="text" 
                                id="address_city" 
                                name="address_city" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="address_region" class="block text-sm font-medium mb-2">Region / State *</label>
                            <input 
                                type="text" 
                                id="address_region" 
                                name="address_region" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="address_postal" class="block text-sm font-medium mb-2">Postal Code *</label>
                            <input 
                                type="text" 
                                id="address_postal" 
                                name="address_postal" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="address_country" class="block text-sm font-medium mb-2">Country *</label>
                        <input 
                            type="text" 
                            id="address_country" 
                            name="address_country" 
                            required
                            class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Bag Type *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <?php
                            $bagTypes = [
                                'SONCIS Homework Bag',
                                'Yobo Bag',
                                'Travel Bag',
                                'Duffel Bag',
                                'Laptop Bag',
                                'Other'
                            ];
                            foreach ($bagTypes as $index => $bagType):
                            ?>
                                <label class="flex items-center gap-3 border border-border rounded-md px-4 py-3">
                                    <input type="radio" name="bag_type" value="<?php echo htmlspecialchars($bagType); ?>" <?php echo $index === 0 ? 'required' : ''; ?> />
                                    <span class="text-sm"><?php echo htmlspecialchars($bagType); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="bag_color" class="block text-sm font-medium mb-2">Bag Color / Pattern *</label>
                            <input 
                                type="text" 
                                id="bag_color" 
                                name="bag_color" 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                        <div>
                            <label for="bag_material" class="block text-sm font-medium mb-2">Material *</label>
                            <input 
                                type="text" 
                                id="bag_material" 
                                name="bag_material" 
                                placeholder="Leather, Canvas, PVC, Fabric..." 
                                required
                                class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Bag Size (optional)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                            <?php foreach (['Small','Medium','Large','Custom'] as $size): ?>
                                <label class="flex items-center gap-2 border border-border rounded-md px-4 py-3">
                                    <input type="radio" name="bag_size" value="<?php echo $size; ?>" />
                                    <span class="text-sm"><?php echo $size; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4">
                            <label for="bag_size_custom" class="block text-sm font-medium mb-2">Custom Dimensions</label>
                            <input 
                                type="text" 
                                id="bag_size_custom" 
                                name="bag_size_custom" 
                                placeholder="Provide custom dimensions (optional)"
                                class="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Repair Needs *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php
                            $repairs = [
                                'Zipper Replacement',
                                'Strap Replacement / Adjustment',
                                'Pocket Repair',
                                'Stitching / Seams Repair',
                                'Waterproofing',
                                'Patch / Hole Repair',
                                'Hardware / Buckles',
                                'Cleaning & Conditioning',
                                'Color Restoration',
                                'Other'
                            ];
                            foreach ($repairs as $repair):
                            ?>
                                <label class="flex items-center gap-2 border border-border rounded-md px-4 py-3">
                                    <input type="checkbox" name="repair_types[]" value="<?php echo htmlspecialchars($repair); ?>" />
                                    <span class="text-sm"><?php echo htmlspecialchars($repair); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label for="issues_description" class="block text-sm font-medium mb-2">Brief Description of Issue(s) *</label>
                        <textarea 
                            id="issues_description" 
                            name="issues_description" 
                            rows="4" 
                            required
                            placeholder="Describe what happened, when it started, and any other useful details."
                            class="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        ></textarea>
                    </div>

                    <div>
                        <label for="reference_images" class="block text-sm font-medium mb-2">Upload Images (optional)</label>
                        <input 
                            type="file" 
                            id="reference_images" 
                            name="reference_images[]" 
                            multiple 
                            accept="image/jpeg,image/png,image/webp"
                            class="w-full text-sm text-muted-foreground"
                        />
                        <p class="text-xs text-muted-foreground mt-2 leading-relaxed">Up to 5 images, JPEG/PNG/WebP, max 5 MB each.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2">Repair Urgency *</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="repair_urgency" value="Standard (3-5 business days)" required />
                                    <span class="text-sm">Standard (3-5 business days)</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="repair_urgency" value="Express (1-2 business days)" />
                                    <span class="text-sm">Express (1-2 business days)</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Return Method *</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="return_method" value="Pickup at SONCIS Workshop" required />
                                    <span class="text-sm">Pickup at SONCIS Workshop</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="return_method" value="Shipping Back (customer pays shipping)" />
                                    <span class="text-sm">Ship back to me</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Payment Option *</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="payment_option" value="Pay After Repair" required />
                                <span class="text-sm">Pay after repair</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="payment_option" value="Pay Partial / Full in Advance" />
                                <span class="text-sm">Pay partial / full in advance</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="additional_notes" class="block text-sm font-medium mb-2">Additional Notes</label>
                        <textarea 
                            id="additional_notes" 
                            name="additional_notes" 
                            rows="3" 
                            placeholder="Access instructions, sentimental notes, delivery preferences, etc."
                            class="w-full px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div id="repair-form-alert" class="hidden px-4 py-3 rounded-md text-sm"></div>
                        <button 
                            type="submit" 
                            id="repair-submit-btn"
                            class="w-full bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition font-semibold uppercase"
                        >
                            Submit Repair Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('repair-order-form').addEventListener('submit', async function(event) {
    event.preventDefault();

    const form = this;
    const submitBtn = document.getElementById('repair-submit-btn');
    const alertBox = document.getElementById('repair-form-alert');
    const originalText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    alertBox.className = 'hidden px-4 py-3 rounded-md text-sm';

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        });

        const result = await response.json();

        alertBox.classList.remove('hidden');
        if (result.success) {
            alertBox.classList.add('bg-green-50', 'text-green-700');
            alertBox.textContent = result.message || 'Repair request submitted successfully.';
            form.reset();
        } else {
            alertBox.classList.add('bg-red-50', 'text-red-700');
            alertBox.textContent = result.message || 'Something went wrong. Please try again.';
        }
    } catch (error) {
        alertBox.classList.remove('hidden');
        alertBox.classList.add('bg-red-50', 'text-red-700');
        alertBox.textContent = 'Unable to submit right now. Kindly try again.';
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>


