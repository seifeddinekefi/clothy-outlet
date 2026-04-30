# Clothy Outlet

Clothy Outlet is a PHP e-commerce website built with a custom MVC architecture. It includes a complete customer shopping flow and an admin panel for day-to-day store operations.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Deploy](https://img.shields.io/github/actions/workflow/status/seifeddinekefi/clothy-outlet/deploy.yml?branch=main&style=flat-square&label=Deploy)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## Project Description

This project targets fashion retail use cases and provides:

- Customer storefront with product browsing, cart, checkout, and account pages.
- Admin dashboard with management tools for products, categories, orders, customers, coupons, subscribers, and settings.
- Secure request handling and session/authentication protections suitable for production hosting.

## Features

### Customer Features

- Product catalog browsing with category and price-range filters.
- **Search autocomplete** — live product suggestions as you type in the navbar.
- Product details with images, size selection, color picking, and quality tier selection.
- Shopping cart with quantity updates and checkout summary.
- Account area: profile, order history, and wishlist.
- **Recently viewed products** — shown at the bottom of each product page.
- **Guest checkout** — complete purchases without creating an account.
- **Guest order tracking** — track orders via unique token URL without login.
- Checkout with Tunisia governorates dropdown and coupon support.
- Payment: Cash on Delivery.
- **Email notifications** — order confirmations, shipping updates, and delivery notifications.
- Optional account creation after guest checkout.
- **Newsletter signup** — email subscription form in the site footer.

### Product Variants

- **Color options** — admin defines available color swatches per product; customers pick on the product page.
- **Quality tiers** — Standard, 180g, 220g, and 250g; admin enables per product.
- Color and quality restrictions: for 220g and 250g tiers only White and Black are offered.
- Size variants with per-size stock tracking (XS–XXL and numeric sizes).

### Admin Features

- Dashboard metrics for revenue, orders, and top products.
- Product CRUD with image gallery, size/color/quality management, badges, and sale pricing.
- Category CRUD.
- Order and payment status updates with **automatic email notifications**.
- Customer management (includes guest customer records).
- Coupon CRUD and application support at checkout.
- **Newsletter subscribers list** — view all emails collected via the footer form.
- Store and account settings management.

### Email System

- **Order confirmation** — sent immediately after checkout (includes tracking link for guests).
- **Welcome email** — sent when customers register or convert from guest to user.
- **Shipped notification** — sent when admin marks order as "shipped".
- **Delivered notification** — sent when admin marks order as "delivered".
- **Password reset** — secure token-based password recovery.
- Configurable drivers: SMTP (Gmail), PHP mail(), or log (development).

### Security

- CSRF protection on all state-changing requests.
- PDO prepared statements throughout.
- Output escaping for XSS mitigation.
- Rate limiting for login and password reset.
- Secure password hashing and environment-based configuration.
- Apache rules block direct access to `.env`, `.sql`, `.log`, and `.git`.

## Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8+ (custom MVC, no framework) |
| Database | MySQL / MariaDB |
| Frontend | HTML, CSS, JavaScript (no build step) |
| Server | Apache (XAMPP compatible) |
| Email | SMTP (Gmail) / PHP mail() / log driver |
| CI/CD | GitHub Actions + FTP Deploy |

## Installation and Setup

### Prerequisites

- PHP 8.0+
- MySQL 8.0+ or MariaDB
- Apache with mod_rewrite enabled
- XAMPP / WAMP / LAMP (or equivalent)

### 1. Clone Repository

```bash
git clone https://github.com/seifeddinekefi/clothy-outlet.git
cd clothy-outlet
```

### 2. Configure Environment

```bash
cp .env.example .env
# Edit .env with your database credentials, app URL, and mail settings
```

Key `.env` values:

```env
APP_URL=http://localhost/clothy/public

DB_HOST=localhost
DB_NAME=clothy_outlet
DB_USER=root
DB_PASS=

MAIL_DRIVER=log          # use smtp for production
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### 3. Create Database and Import Schema

Fresh install:

```bash
mysql -u root -p < database/clothy_outlet.sql
mysql -u root -p clothy_outlet < database/seed.sql   # optional sample data
```

Existing database — run migrations in order:

```bash
mysql -u root -p clothy_outlet < database/migrate_guest_checkout.sql
mysql -u root -p clothy_outlet < database/migrate_colors_qualities.sql
mysql -u root -p clothy_outlet < database/migrate_subscribers.sql
```

### 4. Serve the App

Place the project under XAMPP's `htdocs/clothy/` and access:

- Storefront: `http://localhost/clothy/public`
- Admin panel: `http://localhost/clothy/public/admin`

## Usage Guide

### Customer

1. Use the navbar search bar for live product suggestions.
2. Browse or filter by category and price.
3. On the product page: select size, color, and quality tier (when applicable).
4. Add to cart and checkout as guest or registered user.
5. Apply a coupon code at checkout.
6. Receive confirmation email; track the order via the link in the email.
7. Subscribe to the newsletter via the footer form for updates.

### Admin

1. Sign in at `/admin`.
2. Manage catalog — add products with color swatches, quality tiers, sizes, and images.
3. Process orders — status changes trigger customer email notifications automatically.
4. View newsletter subscribers at `/admin/subscribers`.
5. Manage customers, coupons, categories, and store settings.

## CI/CD Pipeline

Defined in `.github/workflows/deploy.yml`.

- **Trigger:** push to `main`
- **Runner:** ubuntu-latest
- **Deploy:** SamKirkland/FTP-Deploy-Action → InfinityFree
- **Secrets required:** `FTP_USERNAME`, `FTP_PASSWORD`
- **Excluded from deploy:** `.env`, `.git`, `CLAUDE.md`

Configure `.env` directly on the host — it is never deployed.

## Project Structure

```
app/        controllers, models, middleware, views
config/     app configuration and route definitions
core/       MVC framework (Router, Controller, Model, Database, View, Session)
database/   SQL schema, migrations, and seed data
public/     web entry point (index.php) and static assets
storage/    application logs
uploads/    user-uploaded product images
```

## Future Improvements

- Full online payment gateway integration.
- Automated test suite.
- Product reviews and ratings.
- Admin analytics charts (revenue over time, orders by status).
- Shipping fee by governorate.

## Contributing

1. Fork the repository.
2. Create a feature branch.
3. Commit and push changes.
4. Open a Pull Request.

## License

MIT License. See [LICENSE](LICENSE).

## Author

Seifeddine Kefi — [@seifeddinekefi](https://github.com/seifeddinekefi) · [LinkedIn](https://www.linkedin.com/in/seifeddine-kefi/)
