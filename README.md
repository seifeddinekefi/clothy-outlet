# Clothy Outlet

Clothy Outlet is a PHP e-commerce website built with a custom MVC architecture. It includes a complete customer shopping flow and an admin panel for day-to-day store operations.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Deploy](https://img.shields.io/github/actions/workflow/status/seifeddinekefi/clothy-outlet/deploy.yml?branch=main&style=flat-square&label=Deploy)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## Project Description

This project targets fashion retail use cases and provides:

- Customer storefront with product browsing, cart, checkout, and account pages.
- Admin dashboard with management tools for products, categories, orders, customers, coupons, and settings.
- Secure request handling and session/authentication protections suitable for production hosting.

## Features

### Customer Features

- Product catalog browsing with category and search support.
- Product details with images, size selection, and stock-aware cart actions.
- Shopping cart with quantity updates and checkout summary.
- Account area: profile, order history, and wishlist.
- **Guest checkout** — complete purchases without creating an account.
- **Guest order tracking** — track orders via unique token URL without login.
- Checkout with Tunisia governorates dropdown and coupon support.
- Payment: Cash on Delivery with "Open the package first, then pay" messaging.
- **Email notifications** — order confirmations, shipping updates, and delivery notifications.
- Optional account creation after guest checkout for returning customers.

### Admin Features

- Dashboard metrics for revenue, orders, and top products.
- Product and category CRUD.
- Order and payment status updates with **automatic email notifications**.
- Customer management (includes guest customer records).
- Coupon CRUD and application support in checkout.
- Store and account settings management.

### Email System

- **Order confirmation** — sent immediately after checkout (includes tracking link for guests).
- **Welcome email** — sent when customers register or convert from guest to user.
- **Shipped notification** — sent when admin marks order as "shipped".
- **Delivered notification** — sent when admin marks order as "delivered".
- **Password reset** — secure token-based password recovery.
- Configurable drivers: SMTP (Gmail), PHP mail(), or log (development).

### Security and Reliability

- CSRF protection for state-changing requests.
- PDO prepared statements to reduce SQL injection risk.
- Output escaping for XSS mitigation.
- Rate limiting for login and password reset actions.
- Secure password hashing and environment-based configuration.

### Recent Improvements

- Guest checkout flow with optional post-purchase account creation.
- Email notification system with Gmail SMTP support.
- Tunisia governorates dropdown for shipping addresses.
- Guest order tracking via secure token URLs.
- Unified price formatting with centralized helper (TND currency).
- Flat shipping fee configuration set to 8.00 TND.
- CI/CD deployment pipeline with GitHub Actions.

## Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8+ (custom MVC) |
| Database | MySQL / MariaDB |
| Frontend | HTML, CSS, JavaScript |
| Server | Apache (XAMPP compatible) |
| Email | SMTP (Gmail) / PHP mail() |
| CI/CD | GitHub Actions + FTP Deploy |

## Installation and Setup

### Prerequisites

- PHP 8.0+
- MySQL 8.0+ or MariaDB
- Apache with mod_rewrite enabled
- XAMPP/WAMP/LAMP (or equivalent)

### 1. Clone Repository

```bash
git clone https://github.com/seifeddinekefi/clothy-outlet.git
cd clothy-outlet
```

### 2. Configure Environment

Linux/macOS:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Update values in .env:

```env
# Application
APP_URL=http://localhost/clothy/public

# Database
DB_HOST=localhost
DB_NAME=clothy_outlet
DB_USER=root
DB_PASS=

# Email (Gmail SMTP example)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Clothy Outlet"
```

For development, use `MAIL_DRIVER=log` to write emails to `storage/logs/mail.log`.

### 3. Create Database and Import Schema

```bash
mysql -u root -p < database/clothy_outlet.sql
```

For existing databases, run the guest checkout migration:

```bash
mysql -u root -p clothy_outlet < database/migrate_guest_checkout.sql
```

Then run the product badge migration:

```bash
mysql -u root -p clothy_outlet < database/migrate_product_badges.sql
```

Optional sample data:

```bash
mysql -u root -p clothy_outlet < database/seed.sql
```

### 4. Serve the App

- Point Apache document root to the public directory.
- Or run through XAMPP with project under htdocs and access via the public entry point.

Default local URLs:

- Storefront: <http://localhost/clothy/public>
- Admin: <http://localhost/clothy/public/admin>

## Usage Guide

### Customer

1. Browse or search products.
2. Add products to cart/wishlist.
3. **Checkout as guest or sign in** — guests only need email, phone, and address.
4. Apply coupon (optional) during checkout.
5. Place order and receive confirmation email.
6. **Track order** via email link (guests) or account pages (registered users).
7. Optionally create an account after checkout for faster future orders.

### Admin

1. Sign in from /admin.
2. Manage catalog (products/categories).
3. Process orders — **status changes trigger customer email notifications**.
4. Manage customers, coupons, and settings.

## CI/CD Pipeline (GitHub Actions)

Deployment workflow is defined in .github/workflows/deploy.yml.

Pipeline behavior:

- Trigger: push to main.
- Runner: ubuntu-latest.
- Steps:
  - Checkout repository.
  - Deploy via SamKirkland/FTP-Deploy-Action to InfinityFree.
- Sensitive credentials are read from repository secrets:
  - FTP_USERNAME
  - FTP_PASSWORD
- Exclusions include .env and git metadata to avoid leaking secrets and unnecessary files.

## Deployment Details

- Current target: InfinityFree via FTP.
- Remote directory: /htdocs/.
- .env is intentionally excluded from deployment and should be configured directly on the host.
- Recommended flow: merge changes into main to trigger automatic deployment.

## Project Structure

```text
app/        controllers, models, middleware, views
config/     app configuration and routes
core/       MVC core classes and helpers
database/   SQL schema, migrations, and seed data
public/     web entry point and static assets
storage/    logs (including mail.log for development)
uploads/    uploaded files
```

## Future Improvements

- Full online payment gateway integration.
- Automated test suite for key flows.
- Product reviews and ratings.
- API layer for mobile/client integrations.

## Contributing

1. Fork the repository.
2. Create a feature branch.
3. Commit and push changes.
4. Open a Pull Request.

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE).

## Author

Seifeddine Kefi

- GitHub: [@seifeddinekefi](https://github.com/seifeddinekefi)
- LinkedIn: [Seifeddine Kefi](https://www.linkedin.com/in/seifeddine-kefi/)
