<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/db-helper.php';

$pageTitle = 'Payment Verification';
$pageDescription = 'Verifying your Paystack payment...';

include INCLUDES_PATH . '/header.php';
?>

<main class="min-h-screen bg-background pt-20">
    <section class="py-12">
        <div class="container mx-auto px-4 max-w-2xl">
            <div id="payment-status" class="text-center py-16">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
                <p class="text-muted-foreground">Verifying your payment...</p>
            </div>
        </div>
    </section>
</main>

<script>
const PAYSTACK_VERIFY_URL = '<?php echo url('paystack-verify.php'); ?>';
const HOME_URL = '<?php echo url(); ?>';

async function verifyPayment() {
    const urlParams = new URLSearchParams(window.location.search);
    const reference = urlParams.get('reference');
    const statusContainer = document.getElementById('payment-status');

    if (!reference) {
        statusContainer.innerHTML = `
            <div class="text-center py-16 border border-destructive rounded-lg bg-destructive/10">
                <p class="text-destructive font-semibold mb-4">Missing Paystack reference</p>
                <p class="text-muted-foreground mb-6">We could not verify your payment. Please contact support.</p>
                <a href="${HOME_URL}" class="bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition uppercase font-semibold">
                    Return Home
                </a>
            </div>
        `;
        return;
    }

    // Check for pending order (matching React version)
    if (!orderData) {
        statusContainer.innerHTML = `
            <div class="text-center py-16 border border-destructive rounded-lg bg-destructive/10">
                <p class="text-destructive font-semibold mb-4">No pending order found</p>
                <p class="text-muted-foreground mb-6">If you already paid, please contact support.</p>
                <a href="${HOME_URL}" class="bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition uppercase font-semibold">
                    Return Home
                </a>
            </div>
        `;
        return;
    }

    // Get pending order data from sessionStorage
    const pendingOrderData = sessionStorage.getItem('pending-order');
    let orderData = null;
    if (pendingOrderData) {
        try {
            orderData = JSON.parse(pendingOrderData);
        } catch (e) {
            console.error('Failed to parse pending order data:', e);
        }
    }

    try {
        const response = await fetch(PAYSTACK_VERIFY_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                reference,
                orderData: orderData 
            }),
        });

        const result = await response.json();

        // Check payment status like React version
        const isPaymentSuccessful = result.success && 
            result.verified && 
            result.payment && 
            result.payment.status?.toLowerCase() === 'success';

        if (isPaymentSuccessful) {
            // Payment verified successfully
            statusContainer.innerHTML = `
                <div class="text-center py-16 border border-primary rounded-lg bg-primary/10">
                    <div class="text-6xl mb-4">✓</div>
                    <h2 class="text-2xl font-bold mb-4 text-primary">Payment Successful!</h2>
                    <p class="text-muted-foreground mb-2">Your payment has been verified.</p>
                    ${result.orderNumber ? `<p class="font-semibold mb-6">Order Number: ${result.orderNumber}</p>` : ''}
                    <a href="${HOME_URL}" class="bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition uppercase font-semibold">
                        Continue Shopping
                    </a>
                </div>
            `;
            
            // Clear cart and pending order if order was created
            if (result.orderCreated) {
                localStorage.removeItem('soncis-cart');
                sessionStorage.removeItem('pending-order');
            }
        } else {
            // Payment verification failed (matching React version error message)
            const errorMsg = result.message || 'Paystack payment verification failed. Please try again.';
            statusContainer.innerHTML = `
                <div class="text-center py-16 border border-destructive rounded-lg bg-destructive/10">
                    <div class="text-6xl mb-4">✗</div>
                    <h2 class="text-2xl font-bold mb-4 text-destructive">Payment Verification Failed</h2>
                    <p class="text-muted-foreground mb-6">${errorMsg}</p>
                    <p class="text-sm text-muted-foreground mb-6">If you have been charged, please contact support with reference: ${reference}</p>
                    <a href="${HOME_URL}" class="bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition uppercase font-semibold">
                        Return Home
                    </a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Payment verification error:', error);
        statusContainer.innerHTML = `
            <div class="text-center py-16 border border-destructive rounded-lg bg-destructive/10">
                <h2 class="text-2xl font-bold mb-4 text-destructive">Verification Error</h2>
                <p class="text-muted-foreground mb-6">An error occurred while verifying your payment. Please contact support.</p>
                <a href="${HOME_URL}" class="bg-primary text-primary-foreground px-6 py-3 rounded-md hover:opacity-90 transition uppercase font-semibold">
                    Return Home
                </a>
            </div>
        `;
    }
}

// Verify payment on page load
document.addEventListener('DOMContentLoaded', verifyPayment);
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
</body>
</html>

