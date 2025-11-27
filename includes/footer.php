<footer class="bg-background text-foreground border-t border-border">
    <div class="container mx-auto px-4 py-12">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- About -->
            <div>
                <div class="mb-4">
                    <img 
                        src="<?php echo asset('assets/images/photo_2025-11-02_05-35-41-removebg-preview.png'); ?>" 
                        alt="Soncis" 
                        class="h-10 w-auto" 
                    />
                </div>
                <p class="text-muted-foreground mb-2 text-sm font-semibold">
                    If it needs a bag, it needs SONCIS.
                </p>
                <p class="text-muted-foreground mb-6 text-sm">
                    Crafting high-quality, affordable bags for every stage of life—school, work, travel, and beyond.
                </p>
                <div>
                    <p class="text-muted-foreground text-sm mb-2">Follow Us:</p>
                    <div class="flex gap-4">
                        <a href="#" class="text-muted-foreground hover:text-primary transition" aria-label="TikTok">
                            <span class="text-sm">TikTok</span>
                        </a>
                        <a href="#" class="text-muted-foreground hover:text-primary transition" aria-label="Instagram">
                            <iconify-icon icon="mdi:instagram" width="20" height="20"></iconify-icon>
                        </a>
                        <a href="#" class="text-muted-foreground hover:text-primary transition" aria-label="Facebook">
                            <iconify-icon icon="mdi:facebook" width="20" height="20"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h5 class="font-semibold uppercase mb-4">Quick Links</h5>
                <ul class="space-y-2">
                    <li><a href="<?php echo url(); ?>" class="text-gray-400 hover:text-white transition text-sm uppercase">Home</a></li>
                    <li><a href="<?php echo url('shop.php'); ?>" class="text-gray-400 hover:text-white transition text-sm uppercase">Shop</a></li>
                    <li><a href="<?php echo url('impact.php'); ?>" class="text-gray-400 hover:text-white transition text-sm uppercase">Impact</a></li>
                    <li><a href="<?php echo url('contact.php'); ?>" class="text-gray-400 hover:text-white transition text-sm uppercase">Contact</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h5 class="font-semibold uppercase mb-4">Contact Us</h5>
                <p class="text-muted-foreground text-sm mb-2">
                    📍 Visit Us: Takoradi, Ghana
                </p>
                <p class="text-muted-foreground text-sm mb-2">
                    📞 Call: <a href="tel:+233533431086" class="hover:text-primary">0533431086</a>
                </p>
                <p class="text-muted-foreground text-sm mb-2">
                    💬 WhatsApp: <a href="#" class="hover:text-primary">Chat with us</a>
                </p>
                <p class="text-muted-foreground text-sm mb-4">
                    ✉️ Email: <a href="mailto:contact@soncis.com" class="hover:text-primary">contact@soncis.com</a>
                </p>
                <p class="text-muted-foreground text-xs">
                    Operating Hours:<br />
                    Monday – Saturday: 8:30 AM – 5:30 PM
                </p>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="border-t border-border pt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                <div class="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 items-start sm:items-center">
                    <span class="text-sm text-muted-foreground">Payment Option:</span>
                    <div class="flex gap-3">
                        <img src="<?php echo asset('assets/images/visa-card.png'); ?>" alt="Visa" class="h-auto w-auto" />
                        <img src="<?php echo asset('assets/images/paypal-card.png'); ?>" alt="PayPal" class="h-auto w-auto" />
                        <img src="<?php echo asset('assets/images/master-card.png'); ?>" alt="Mastercard" class="h-auto w-auto" />
                    </div>
                </div>
                <div class="text-center md:text-right text-sm text-muted-foreground">
                    <p>© <?php echo date('Y'); ?> SONCIS. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
