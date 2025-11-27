<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/db-helper.php';

$pageTitle = 'Checkout';
$pageDescription = 'Complete your SONCIS order with flexible payment options.';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-16 sm:pt-20">
    <section class="py-12">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="mb-6 text-sm text-muted-foreground">
                <a href="<?php echo url(); ?>" class="hover:text-primary">Home</a>
                <span class="mx-1">/</span>
                <span class="hover:text-primary">Pages</span>
                <span class="mx-1">/</span>
                <span class="text-foreground">Checkout</span>
            </div>

            <h1 class="text-4xl md:text-5xl font-bold mb-12 uppercase">Checkout</h1>

            <div id="checkout-container"></div>
        </div>
    </section>

    <?php include INCLUDES_PATH . '/newsletter-section.php'; ?>
</main>

<script>
const SHOP_URL = '<?php echo url('shop.php'); ?>';
const HOME_URL = '<?php echo url(); ?>';
const BASE_URL = '<?php echo url(); ?>';
const CHECKOUT_HANDLER_URL = '<?php echo url('checkout-handler.php'); ?>';
const PAYSTACK_INIT_URL = '<?php echo url('paystack-init.php'); ?>';

// JavaScript function to get image URL (mimics PHP getImageUrl)
function getImageUrl(imagePath) {
    if (!imagePath || imagePath === '') {
        return BASE_URL + 'assets/images/placeholder.jpg';
    }
    // If already an absolute URL, return as-is
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
        return imagePath;
    }
    // If path starts with /assets/, use it as is
    if (imagePath.startsWith('/assets/')) {
        return BASE_URL + imagePath.substring(1);
    }
    // Default: assume it's in assets/images/
    const filename = imagePath.split('/').pop();
    return BASE_URL + 'assets/images/' + filename;
}

const parsePriceString = (priceString) => {
    if (!priceString) return 0;
    return parseFloat(String(priceString).replace(/[₵$,]/g, '')) || 0;
};

const formatCurrency = (value) => '₵' + Number(value || 0).toLocaleString('en-GH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

function renderCheckout() {
    const cart = JSON.parse(localStorage.getItem('soncis-cart') || '[]');
    const container = document.getElementById('checkout-container');

    if (!cart.length) {
        container.innerHTML = `
            <div class="text-center py-16 border border-dashed border-border rounded-lg">
                <p class="text-muted-foreground mb-6">Your cart is empty.</p>
                <a href="${SHOP_URL}" class="bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition uppercase font-semibold">
                    Continue Shopping
                </a>
            </div>
        `;
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + parsePriceString(item.price) * (item.quantity || 1), 0);
    // Show first item prominently with large image, or all items in a list
    const firstItem = cart[0];
    const firstItemImageUrl = getImageUrl(firstItem?.image || firstItem?.imageUrl || '');
    const firstItemTotal = firstItem ? parsePriceString(firstItem.price) * (firstItem.quantity || 1) : 0;
    
    const orderItemsHtml = cart.length > 0 ? `
        <div class="relative w-full rounded-lg overflow-hidden bg-muted">
            <div class="relative w-full h-48 sm:h-64 md:h-80 rounded-lg overflow-hidden">
                <img src="${firstItemImageUrl}" alt="${firstItem.name.replace(/"/g, '&quot;')}" class="w-full h-full object-cover" onerror="this.src='${BASE_URL}assets/images/placeholder.jpg'" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 text-white">
                    <p class="font-bold text-base sm:text-lg md:text-xl uppercase mb-1 sm:mb-2">${firstItem.name}</p>
                    <p class="text-sm sm:text-base md:text-lg font-semibold mb-1 sm:mb-2">${firstItem.price}</p>
                    <p class="text-xs sm:text-sm opacity-90">Quantity: ${firstItem.quantity || 1}</p>
                </div>
            </div>
        </div>
        ${cart.length > 1 ? `<div class="pt-4 mt-4 border-t border-border space-y-2">
            <p class="text-sm font-semibold text-foreground mb-2">Additional Items:</p>
            ${cart.slice(1).map(item => {
                const itemTotal = parsePriceString(item.price) * (item.quantity || 1);
                return `<div class="flex justify-between text-sm">
                    <span class="text-foreground">${item.name} x ${item.quantity || 1}</span>
                    <span class="text-muted-foreground">${formatCurrency(itemTotal)}</span>
                </div>`;
            }).join('')}
        </div>` : ''}
    ` : '';

    container.innerHTML = `
        <form id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <div class="lg:col-span-2">
                <div class="space-y-6">
                    <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 uppercase">Billing Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold">First Name <span class="text-destructive">*</span></label>
                            <input type="text" name="firstName" required class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold">Last Name <span class="text-destructive">*</span></label>
                            <input type="text" name="lastName" required class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold">Company Name (optional)</label>
                        <input type="text" name="company" class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold">Country / Region <span class="text-destructive">*</span></label>
                        <select name="country" class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="Ghana" selected>Ghana</option>
                            <option value="Nigeria">Nigeria</option>
                            <option value="Kenya">Kenya</option>
                            <option value="South Africa">South Africa</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block mb-2 text-sm font-semibold">Street Address <span class="text-destructive">*</span></label>
                        <input type="text" name="address1" placeholder="House number and street name" required class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                        <input type="text" name="address2" placeholder="Apartment, suite, etc. (optional)" class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold">Town / City <span class="text-destructive">*</span></label>
                            <input type="text" name="city" required class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold">State / Region <span class="text-destructive">*</span></label>
                            <input type="text" name="state" required class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="sm:col-span-2 md:col-span-1">
                            <label class="block mb-2 text-sm font-semibold">Postal Code <span class="text-destructive">*</span></label>
                            <input type="text" name="zip" required class="w-full px-4 py-3 border border-border rounded bg-background focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold">Phone <span class="text-destructive">*</span></label>
                            <input type="tel" name="phone" required class="w-full px-4 py-3 border border-border rounded bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div id="email-or-mobile-container">
                            <label class="block mb-2 text-sm font-semibold">Email Address <span class="text-destructive">*</span></label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-border rounded bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>
                    <div id="mobile-money-field" class="hidden">
                        <label class="block mb-2 text-sm font-semibold">Mobile Money Number <span class="text-destructive">*</span></label>
                        <input type="tel" name="mobileMoneyNumber" class="w-full px-4 py-3 border border-border rounded bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Enter the wallet number to charge">
                        <p class="text-xs text-muted-foreground mt-1">We will send a Paystack mobile money prompt to this number.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-8">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 uppercase text-foreground">Additional Information</h2>
                        <textarea name="notes" rows="4" placeholder="Order notes (optional)" class="w-full px-4 py-3 border border-border rounded bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary resize-y"></textarea>
                    </div>

                    <div class="p-4 sm:p-6 border border-border rounded-lg bg-muted/30">
                        <h2 class="text-lg sm:text-xl font-bold uppercase mb-4 sm:mb-6 text-foreground">Order Summary</h2>
                        <div class="w-full">${orderItemsHtml}</div>
                    </div>

                    <div class="p-4 sm:p-6 border border-border rounded-lg bg-muted/30">
                        <h2 class="text-lg sm:text-xl font-bold uppercase mb-4 sm:mb-6 text-foreground">Cart Totals</h2>
                        <div class="space-y-4">
                            ${cart.map(item => {
                                const itemTotal = parsePriceString(item.price) * (item.quantity || 1);
                                return `<div class="pb-3 ${cart.indexOf(item) < cart.length - 1 ? 'border-b border-border' : ''}">
                                    <p class="font-semibold text-sm uppercase text-foreground mb-1">${item.name}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-muted-foreground">${item.quantity || 1} × ${item.price}</span>
                                        <span class="font-semibold text-foreground">${formatCurrency(itemTotal)}</span>
                                    </div>
                                </div>`;
                            }).join('')}
                            <div class="flex justify-between items-center pt-2 border-t border-border">
                                <span class="text-lg font-bold text-foreground">Total</span>
                                <span class="text-lg font-bold text-primary">${formatCurrency(subtotal)}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-lg sm:text-xl font-bold uppercase mb-4 sm:mb-6 text-foreground">Payment Methods</h2>
                        <div class="space-y-3 sm:space-y-4">
                            <label class="payment-option group flex items-start gap-3 sm:gap-4 p-4 sm:p-5 border-2 border-border rounded-lg cursor-pointer transition-all duration-200 bg-white hover:border-primary hover:shadow-sm">
                                <input type="radio" name="paymentMethod" value="bank-transfer" class="mt-0.5 flex-shrink-0" checked>
                                <div class="flex-1 min-w-0">
                                    <span class="font-bold text-sm sm:text-base block mb-1 sm:mb-1.5 text-foreground">Direct Bank Transfer</span>
                                    <span class="text-xs sm:text-sm leading-relaxed text-muted-foreground">Make your payment directly into our bank account. Please use your Order ID as the payment reference.</span>
                                </div>
                            </label>
                            <label class="payment-option group flex items-start gap-3 sm:gap-4 p-4 sm:p-5 border-2 border-border rounded-lg cursor-pointer transition-all duration-200 bg-white hover:border-primary hover:shadow-sm">
                                <input type="radio" name="paymentMethod" value="cash" class="mt-0.5 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <span class="font-bold text-sm sm:text-base block mb-1 sm:mb-1.5 text-foreground">Cash on Delivery</span>
                                    <span class="text-xs sm:text-sm leading-relaxed text-muted-foreground">Pay with cash upon delivery. Available for orders within Accra and surrounding areas.</span>
                                </div>
                            </label>
                            <label class="payment-option group flex items-start gap-3 sm:gap-4 p-4 sm:p-5 border-2 border-border rounded-lg cursor-pointer transition-all duration-200 bg-white hover:border-primary hover:shadow-sm">
                                <input type="radio" name="paymentMethod" value="mobile-money" class="mt-0.5 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <span class="font-bold text-sm sm:text-base block mb-1 sm:mb-1.5 text-foreground">Mobile Money (MTN/Vodafone)</span>
                                    <span class="text-xs sm:text-sm leading-relaxed text-muted-foreground">Pay via mobile money transfer. Secure and instant payment processing.</span>
                                </div>
                            </label>
                            <label class="payment-option group flex items-start gap-3 sm:gap-4 p-4 sm:p-5 border-2 border-border rounded-lg cursor-pointer transition-all duration-200 bg-white hover:border-primary hover:shadow-sm">
                                <input type="radio" name="paymentMethod" value="paypal" class="mt-0.5 flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <span class="font-bold text-sm sm:text-base block mb-1 sm:mb-1.5 text-foreground">PayPal</span>
                                    <span class="text-xs sm:text-sm leading-relaxed text-muted-foreground">Pay securely with your PayPal account or credit card.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full px-4 sm:px-6 py-3 sm:py-4 bg-secondary text-white rounded-lg hover:opacity-90 transition font-bold uppercase text-sm sm:text-base disabled:opacity-50">
                        Place an Order
                    </button>
                </div>
            </div>
        </form>
    `;

    const form = document.getElementById('checkout-form');
    const paymentRadios = Array.from(form.querySelectorAll('input[name="paymentMethod"]'));
    const mobileField = form.querySelector('#mobile-money-field');
    const mobileInput = form.querySelector('input[name="mobileMoneyNumber"]');
    const emailContainer = form.querySelector('#email-or-mobile-container');

    const toggleMobileField = () => {
        const selected = (form.querySelector('input[name="paymentMethod"]:checked') || {}).value;
        if (selected === 'mobile-money') {
            if (mobileField) mobileField.classList.remove('hidden');
            if (mobileInput) mobileInput.required = true;
        } else {
            if (mobileField) mobileField.classList.add('hidden');
            if (mobileInput) {
                mobileInput.required = false;
                mobileInput.value = '';
            }
        }
    };

    paymentRadios.forEach((radio) => {
        radio.addEventListener('change', toggleMobileField);
        radio.addEventListener('change', updatePaymentOptionStyles);
    });
    toggleMobileField();
    updatePaymentOptionStyles();

    form.addEventListener('submit', (event) => handleCheckoutSubmit(event, cart, subtotal));
}

function updatePaymentOptionStyles() {
    const form = document.getElementById('checkout-form');
    if (!form) return;
    const paymentLabels = form.querySelectorAll('.payment-option');
    
    paymentLabels.forEach((label) => {
        const radio = label.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            label.classList.add('border-primary', 'bg-primary/5', 'shadow-md');
            label.classList.remove('border-border', 'bg-white', 'hover:shadow-sm');
        } else {
            label.classList.remove('border-primary', 'bg-primary/5', 'shadow-md');
            label.classList.add('border-border', 'bg-white', 'hover:shadow-sm');
        }
    });
}

async function handleCheckoutSubmit(event, cart, subtotal) {
    event.preventDefault();
    const form = event.currentTarget;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.disabled = true;
    submitButton.textContent = 'Placing Order...';

    const formData = new FormData(form);
    const paymentMethod = formData.get('paymentMethod') || 'cash';
    let mobileMoneyNumber = (formData.get('mobileMoneyNumber') || '').replace(/\D/g, '');

    if (paymentMethod === 'mobile-money') {
        if (!mobileMoneyNumber || mobileMoneyNumber.length < 9 || mobileMoneyNumber.length > 15) {
            alert('Mobile money numbers should contain 9 to 15 digits.');
            submitButton.disabled = false;
            submitButton.textContent = originalText;
            return;
        }
    }

    const shippingAddress = [
        formData.get('address1'),
        formData.get('address2'),
        formData.get('city'),
        formData.get('state'),
        formData.get('zip'),
        formData.get('country'),
    ].filter(Boolean).join(', ');

    const orderPayload = {
        customerName: `${formData.get('firstName')} ${formData.get('lastName')}`.trim(),
        customerEmail: formData.get('email'),
        customerPhone: formData.get('phone'),
        company: formData.get('company') || '',
        shippingAddress,
        billingAddress: shippingAddress,
        notes: formData.get('notes') || '',
        total: formatCurrency(subtotal),
        paymentMethod,
        mobileMoneyNumber,
        paymentDetails: paymentMethod === 'bank-transfer' ? 'Customer will complete bank transfer manually.' : '',
        items: cart.map((item) => ({
            id: item.id,
            name: item.name,
            price: item.price,
            quantity: item.quantity || 1,
        })),
        meta: {
            company: formData.get('company') || '',
            notes: formData.get('notes') || '',
        },
    };

    // Handle mobile money payments with Paystack
    if (paymentMethod === 'mobile-money') {
        try {
            submitButton.textContent = 'Initializing Payment...';
            
            // Initialize Paystack payment
            const paystackResponse = await fetch(PAYSTACK_INIT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: formData.get('email'),
                    amount: subtotal, // Numeric amount (will be converted to pesewas in PHP)
                    phone: mobileMoneyNumber,
                    name: `${formData.get('firstName')} ${formData.get('lastName')}`.trim(),
                }),
            });

            const paystackResult = await paystackResponse.json();

            if (!paystackResponse.ok || !paystackResult.success) {
                throw new Error(paystackResult.message || 'Failed to initialize Paystack payment.');
            }

            // Save order data to sessionStorage for after payment
            sessionStorage.setItem('pending-order', JSON.stringify({
                ...orderPayload,
                paymentMethod: 'mobile-money',
                reference: paystackResult.reference,
                mobileMoneyNumber: mobileMoneyNumber,
            }));

            // Redirect to Paystack
            window.location.href = paystackResult.authorizationUrl;
            return;
        } catch (error) {
            console.error('Paystack initialization error:', error);
            alert(`Mobile money payment failed: ${error.message || 'Unable to reach Paystack at the moment.'}`);
            submitButton.disabled = false;
            submitButton.textContent = originalText;
            return;
        }
    }

    // For non-mobile-money payments, proceed with regular order creation
    try {
        const response = await fetch(CHECKOUT_HANDLER_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderPayload),
        });

        // Check if response is ok before parsing
        if (!response.ok) {
            let errorMessage = `HTTP error! status: ${response.status}`;
            try {
                const errorText = await response.text();
                if (errorText) {
                    const error = JSON.parse(errorText);
                    errorMessage = error.message || errorMessage;
                }
            } catch (e) {
                // If parsing fails, use the text or default message
                const errorText = await response.text().catch(() => '');
                errorMessage = errorText || errorMessage;
            }
            throw new Error(errorMessage);
        }

        // Parse JSON response
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const text = await response.text();
            if (!text || text.trim() === '') {
                throw new Error('Empty response from server');
            }
            
            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON response:', text);
                throw new Error('Invalid response from server');
            }

            if (result.success) {
                localStorage.removeItem('soncis-cart');
                alert(`Order placed successfully! Order Number: ${result.orderNumber}`);
                window.location.href = HOME_URL;
            } else {
                let errorMsg = result.message || 'Failed to place order. Please try again.';
                // Show debug error if available (for troubleshooting)
                if (result.debug) {
                    console.error('Order error details:', result.debug);
                    errorMsg += '\n\nDebug: ' + result.debug;
                }
                alert(errorMsg);
            }
        } else {
            throw new Error('Invalid response format from server');
        }
    } catch (error) {
        console.error('Order creation error:', error);
        const errorMsg = error.message || 'Unknown error';
        alert(`Failed to place order: ${errorMsg}`);
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
}

document.addEventListener('DOMContentLoaded', renderCheckout);
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>
