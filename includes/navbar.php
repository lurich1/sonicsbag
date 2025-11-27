<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
if ($currentPage === 'index') $currentPage = 'home';
?>
<nav id="soncis-navbar" class="navbar-root fixed top-0 left-0 right-0 w-full z-50 transition-all duration-300" data-transparent="false">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="<?php echo url(); ?>" class="flex-shrink-0 flex items-center gap-2">
                <img 
                    src="<?php echo asset('assets/images/photo_2025-11-02_05-35-41-removebg-preview.png'); ?>" 
                    alt="SONCIS" 
                    class="h-10 w-auto" 
                />
                <span class="hidden lg:block text-sm text-muted-foreground italic">
                    If it needs a bag, it needs SONCIS.
                </span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-6 lg:gap-8">
                <a href="<?php echo url(); ?>" class="nav-link hover:text-primary transition-colors font-medium <?php echo $currentPage === 'home' ? 'text-primary' : 'text-foreground'; ?>">Home</a>
                <a href="<?php echo url('about.php'); ?>" class="nav-link hover:text-primary transition-colors font-medium <?php echo $currentPage === 'about' ? 'text-primary' : 'text-foreground'; ?>">About</a>
                <a href="<?php echo url('shop.php'); ?>" class="nav-link hover:text-primary transition-colors font-medium <?php echo $currentPage === 'shop' ? 'text-primary' : 'text-foreground'; ?>">Shop</a>
                <a href="<?php echo url('custom-orders.php'); ?>" class="nav-link hover:text-primary transition-colors font-medium <?php echo $currentPage === 'custom-orders' ? 'text-primary' : 'text-foreground'; ?>">Custom Orders</a>
                <a href="<?php echo url('bag-repair.php'); ?>" class="nav-link hover:text-primary transition-colors font-medium <?php echo $currentPage === 'bag-repair' ? 'text-primary' : 'text-foreground'; ?>">Bag Repair</a>
                <a href="<?php echo url('impact.php'); ?>" class="nav-link hover:text-primary transition-colors font-medium <?php echo $currentPage === 'impact' ? 'text-primary' : 'text-foreground'; ?>">Impact</a>
                <a href="<?php echo url('contact.php'); ?>" class="nav-link hover:text-primary transition-colors font-medium <?php echo $currentPage === 'contact' ? 'text-primary' : 'text-foreground'; ?>">Contact</a>
            </div>

            <!-- Right Icons -->
            <div class="flex items-center gap-3 sm:gap-4 md:gap-6">
                <button onclick="openSearchModal()" class="hover:text-primary transition p-2" aria-label="Search">
                    <iconify-icon icon="mdi:magnify" width="20" height="20" class="sm:w-6 sm:h-6"></iconify-icon>
                </button>
                <a href="<?php echo url('wishlist.php'); ?>" class="hover:text-primary transition inline-flex p-2" aria-label="Wishlist">
                    <iconify-icon icon="mdi:heart-outline" width="20" height="20" class="sm:w-6 sm:h-6"></iconify-icon>
                </a>
                <a href="<?php echo url('cart.php'); ?>" class="hover:text-primary transition inline-flex p-2" aria-label="Cart">
                    <iconify-icon icon="mdi:cart-outline" width="20" height="20" class="sm:w-6 sm:h-6"></iconify-icon>
                </a>
                <button
                    onclick="toggleMobileMenu()"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded border border-border hover:bg-muted transition"
                    aria-label="Toggle navigation menu"
                    id="mobile-menu-btn"
                >
                    <iconify-icon icon="mdi:menu" width="24" height="24" id="menu-icon"></iconify-icon>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden fixed top-20 left-0 right-0 bg-white shadow-lg z-40 transform -translate-y-full opacity-0 invisible transition-all duration-300" id="mobile-menu" style="box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);">
            <div class="p-6">
                <ul class="list-none flex flex-col">
                    <li class="mb-4"><a href="<?php echo url(); ?>" onclick="toggleMobileMenu()" class="block py-3 px-4 text-foreground font-medium rounded-lg transition <?php echo $currentPage === 'home' ? 'text-primary bg-primary/10' : 'hover:bg-primary/10 hover:text-primary'; ?>">Home</a></li>
                    <li class="mb-4"><a href="<?php echo url('shop.php'); ?>" onclick="toggleMobileMenu()" class="block py-3 px-4 text-foreground font-medium rounded-lg transition <?php echo $currentPage === 'shop' ? 'text-primary bg-primary/10' : 'hover:bg-primary/10 hover:text-primary'; ?>">Shop</a></li>
                    <li class="mb-4"><a href="<?php echo url('about.php'); ?>" onclick="toggleMobileMenu()" class="block py-3 px-4 text-foreground font-medium rounded-lg transition <?php echo $currentPage === 'about' ? 'text-primary bg-primary/10' : 'hover:bg-primary/10 hover:text-primary'; ?>">About</a></li>
                    <li class="mb-4"><a href="<?php echo url('custom-orders.php'); ?>" onclick="toggleMobileMenu()" class="block py-3 px-4 text-foreground font-medium rounded-lg transition <?php echo $currentPage === 'custom-orders' ? 'text-primary bg-primary/10' : 'hover:bg-primary/10 hover:text-primary'; ?>">Custom Orders</a></li>
                    <li class="mb-4"><a href="<?php echo url('bag-repair.php'); ?>" onclick="toggleMobileMenu()" class="block py-3 px-4 text-foreground font-medium rounded-lg transition <?php echo $currentPage === 'bag-repair' ? 'text-primary bg-primary/10' : 'hover:bg-primary/10 hover:text-primary'; ?>">Bag Repair</a></li>
                    <li class="mb-4"><a href="<?php echo url('impact.php'); ?>" onclick="toggleMobileMenu()" class="block py-3 px-4 text-foreground font-medium rounded-lg transition <?php echo $currentPage === 'impact' ? 'text-primary bg-primary/10' : 'hover:bg-primary/10 hover:text-primary'; ?>">Impact</a></li>
                    <li class="mb-4"><a href="<?php echo url('contact.php'); ?>" onclick="toggleMobileMenu()" class="block py-3 px-4 text-foreground font-medium rounded-lg transition <?php echo $currentPage === 'contact' ? 'text-primary bg-primary/10' : 'hover:bg-primary/10 hover:text-primary'; ?>">Contact</a></li>
                </ul>
                <div class="flex justify-around pt-6 mt-4 border-t border-border">
                    <button onclick="openSearchModal(); toggleMobileMenu();" class="flex flex-col items-center gap-1 text-foreground text-sm py-2 px-4 rounded-lg transition hover:bg-primary/10 hover:text-primary">
                        <iconify-icon icon="mdi:magnify" width="20" height="20"></iconify-icon>
                        <span>Search</span>
                    </button>
                    <a href="<?php echo url('wishlist.php'); ?>" onclick="toggleMobileMenu()" class="flex flex-col items-center gap-1 text-foreground text-sm py-2 px-4 rounded-lg transition hover:bg-primary/10 hover:text-primary">
                        <iconify-icon icon="mdi:heart-outline" width="20" height="20"></iconify-icon>
                        <span>Wishlist</span>
                    </a>
                    <a href="<?php echo url('cart.php'); ?>" onclick="toggleMobileMenu()" class="flex flex-col items-center gap-1 text-foreground text-sm py-2 px-4 rounded-lg transition hover:bg-primary/10 hover:text-primary relative">
                        <iconify-icon icon="mdi:cart-outline" width="20" height="20"></iconify-icon>
                        <span>Cart</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Modal -->
    <div id="search-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-background p-6 rounded-lg max-w-2xl w-full mx-4">
            <div class="flex items-center gap-4 mb-4">
                <input 
                    type="text" 
                    id="search-input" 
                    placeholder="Search products..." 
                    class="flex-1 px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                    onkeyup="handleSearch(event)"
                />
                <button onclick="closeSearchModal()" class="p-2 hover:bg-muted rounded">
                    <iconify-icon icon="mdi:close" width="24" height="24"></iconify-icon>
                </button>
            </div>
            <div id="search-results" class="max-h-96 overflow-y-auto"></div>
        </div>
    </div>
</nav>

<script>
// Get base path and URLs from PHP
const basePath = '<?php echo isset($GLOBALS["BASE_PATH"]) ? $GLOBALS["BASE_PATH"] : ""; ?>';
const shopUrl = '<?php echo url("shop.php"); ?>';
const homeUrl = '<?php echo url(); ?>';
const currentPath = window.location.pathname;
const isHomePage = currentPath === homeUrl || currentPath === basePath + '/' || currentPath === basePath + '/index.php' || currentPath === '/' || currentPath === '/index.php';

function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    const btn = document.getElementById('mobile-menu-btn');
    const icon = document.getElementById('menu-icon');
    
    if (menu.classList.contains('-translate-y-full')) {
        // Open menu - slide down
        menu.classList.remove('-translate-y-full', 'opacity-0', 'invisible');
        menu.classList.add('translate-y-0', 'opacity-100', 'visible');
        icon.setAttribute('icon', 'mdi:close');
        document.body.style.overflow = 'hidden';
    } else {
        // Close menu - slide up
        menu.classList.remove('translate-y-0', 'opacity-100', 'visible');
        menu.classList.add('-translate-y-full', 'opacity-0', 'invisible');
        icon.setAttribute('icon', 'mdi:menu');
        document.body.style.overflow = '';
    }
}

function openSearchModal() {
    document.getElementById('search-modal').classList.remove('hidden');
    document.getElementById('search-modal').classList.add('flex');
    document.getElementById('search-input').focus();
}

function closeSearchModal() {
    document.getElementById('search-modal').classList.add('hidden');
    document.getElementById('search-modal').classList.remove('flex');
}

function handleSearch(e) {
    if (e.key === 'Enter') {
        const query = e.target.value;
        if (query.trim()) {
            window.location.href = shopUrl + '?search=' + encodeURIComponent(query);
        }
    }
}

// Make navbar transparent on home page when scrolled to top, and add shadow when scrolled
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('soncis-navbar');
    const scrollY = window.scrollY;
    
    if (isHomePage) {
        if (scrollY < 120) {
            navbar.setAttribute('data-transparent', 'true');
            navbar.classList.remove('navbar-scrolled');
        } else {
            navbar.setAttribute('data-transparent', 'false');
            navbar.classList.add('navbar-scrolled');
        }
    } else {
        // On other pages, always show solid background
        navbar.setAttribute('data-transparent', 'false');
        if (scrollY > 10) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    }
});

// Initialize on page load
window.addEventListener('load', function() {
    const navbar = document.getElementById('soncis-navbar');
    const scrollY = window.scrollY;
    
    if (isHomePage) {
        if (scrollY < 120) {
            navbar.setAttribute('data-transparent', 'true');
        } else {
            navbar.setAttribute('data-transparent', 'false');
            navbar.classList.add('navbar-scrolled');
        }
    } else {
        navbar.setAttribute('data-transparent', 'false');
        if (scrollY > 10) {
            navbar.classList.add('navbar-scrolled');
        }
    }
});
</script>

