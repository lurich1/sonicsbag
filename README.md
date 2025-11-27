# SONCIS PHP Frontend & Backend

A complete PHP-based e-commerce website for SONCIS with direct database access (no API required).

## Structure

```
sonics bag/
├── config.php              # Configuration file
├── index.php               # Home page
├── shop.php                # Shop page
├── about.php               # About page
├── contact.php             # Contact page
├── contact-handler.php     # Contact form handler
├── product.php             # Product detail page
├── cart.php                # Shopping cart
├── checkout.php            # Checkout page
├── checkout-handler.php    # Order processing
├── newsletter-handler.php  # Newsletter signup endpoint
├── wishlist.php            # Wishlist
├── custom-orders.php       # Custom orders page
├── custom-order-handler.php # Custom order handler
├── impact.php              # Impact page
├── includes/               # Reusable components
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   ├── hero-banner.php
│   ├── features-grid.php
│   ├── product-grid.php
│   ├── testimonials-carousel.php
│   ├── blog-section.php
│   ├── newsletter-section.php
│   ├── instagram-section.php
│   ├── db.php              # Database connection
│   ├── db-helper.php       # Database helper functions
│   └── api-helper.php      # Product helper functions (uses db-helper)
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── store.js        # Shared cart/wishlist helpers
│   └── images/             # Copy images from public/images/
├── database/               # Database files (create this folder)
│   └── soncis.db          # SQLite database (will be created)
└── .htaccess              # Apache configuration
```

## Setup Instructions

### 1. Database Setup

#### Option A: SQLite (Recommended for Development)
1. Create the `database` folder in the `sonics bag` directory
2. Run the bundled setup script to generate the schema and default admin account:
   ```bash
   php database/setup.php
   ```
   - This script is idempotent (safe to re-run) and outputs the status of each table creation.
   - A default admin user (`admin` / `admin123`) will be created if none exists.
3. If you prefer raw SQL, you can also execute `database/schema.sqlite.sql` against a blank database file.

#### Option B: MySQL
1. Update `includes/db.php`:
   ```php
   define('DB_TYPE', 'mysql');
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'soncis');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```
2. Import the database schema from `backend/SoncisAPI/create-tables-mysql.sql`

#### Option C: SQL Server
1. Update `includes/db.php`:
   ```php
   define('DB_TYPE', 'sqlserver');
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'soncis');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```
2. Import the database schema from `backend/SoncisAPI/create-tables-sqlserver.sql`

### 2. Copy Images
Copy all images from `public/images/` to `assets/images/`

### 3. Set Up Web Server

#### Apache
- Ensure mod_rewrite is enabled
- The `.htaccess` file is already configured

#### Nginx
Configure URL rewriting:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

#### PHP Built-in Server
```bash
cd "sonics bag"
php -S localhost:8000
```

### 4. File Permissions
Ensure the `database` folder is writable by the web server:
```bash
chmod 755 database
chmod 644 database/soncis.db
```

## Features

- ✅ Direct database access (no API required)
- ✅ Responsive design
- ✅ Product listing and details
- ✅ Shopping cart (localStorage)
- ✅ Order processing
- ✅ Wishlist (localStorage)
- ✅ Contact form
- ✅ Custom order form
- ✅ SEO-friendly structure
- ✅ Mobile-responsive navigation
- ✅ Support for SQLite, MySQL, and SQL Server

## Database Tables

The system uses these tables:
- `Products` - Product catalog
- `Orders` - Customer orders
- `OrderItems` - Order line items
- `SiteContents` - Site configuration
- `AdminUsers` - Admin users (for future admin panel)

## Configuration

Edit `includes/db.php` to configure your database connection.

Edit `config.php` to configure site settings.

## Notes

- Cart and wishlist data is stored in browser localStorage
- Orders are saved directly to the database
- Contact and custom order forms send emails (requires mail server configuration)
- Images should be copied from the main project's `public/images/` folder

## Troubleshooting

### Database Connection Errors
- Check database file permissions
- Verify database path in `includes/db.php`
- Ensure PDO extensions are enabled in PHP

### Images Not Loading
- Verify images are copied to `assets/images/`
- Check file paths in the PHP files
- Ensure web server has read permissions

### Forms Not Working
- Check PHP error logs
- Verify mail server configuration for email sending
- Ensure JavaScript is enabled in browser
