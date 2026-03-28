# 🛍️ Clothy Outlet

A modern, full-featured e-commerce platform built with PHP for fashion retail. Clothy Outlet provides a complete online shopping experience with a powerful admin dashboard for store management.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

---

## ✨ Features

### 🛒 Customer Features

- **Product Browsing** - Browse products by category with search and filtering
- **Shopping Cart** - Add/remove items, update quantities
- **User Accounts** - Registration, login, profile management
- **Order Management** - Track orders, view order history
- **Wishlist** - Save favorite items for later
- **Secure Checkout** - Safe and streamlined checkout process

### 🔧 Admin Dashboard

- **Dashboard Analytics** - Sales overview, order statistics, revenue charts
- **Product Management** - Add, edit, delete products with image uploads
- **Category Management** - Organize products into categories
- **Order Management** - View, update, and process customer orders
- **Customer Management** - View and manage customer accounts
- **Admin Roles** - Role-based access control for admin users

### 🔒 Security Features

- **CSRF Protection** - Token-based form protection
- **SQL Injection Prevention** - PDO prepared statements throughout
- **XSS Protection** - Output escaping with `htmlspecialchars()`
- **Rate Limiting** - Brute-force protection on login endpoints
- **Secure Sessions** - HttpOnly, SameSite, and Secure cookie flags
- **Password Hashing** - bcrypt with proper cost factor
- **Environment Variables** - Secrets stored in `.env` file

---

## 🛠️ Tech Stack

| Layer             | Technology                      |
| ----------------- | ------------------------------- |
| **Backend**       | PHP 8.0+ (Custom MVC Framework) |
| **Database**      | MySQL 8.0+ / MariaDB            |
| **Frontend**      | HTML5, CSS3, JavaScript         |
| **CSS Framework** | Bootstrap 5                     |
| **Server**        | Apache (XAMPP compatible)       |

---

## 📦 Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 8.0+ or MariaDB
- Apache web server (with mod_rewrite enabled)
- XAMPP, WAMP, or similar local development environment

### Steps

1. **Clone the repository**

   ```bash
   git clone https://github.com/YOUR_USERNAME/clothy-outlet.git
   cd clothy-outlet
   ```

2. **Set up the database**

   ```bash
   # Create database and import schema
   mysql -u root -p < database/clothy_outlet.sql

   # Import sample data (optional)
   mysql -u root -p clothy_outlet < database/seed.sql
   ```

3. **Configure environment**

   ```bash
   # Copy the example environment file
   cp .env.example .env

   # Edit .env with your database credentials
   ```

4. **Configure your `.env` file**

   ```env
   APP_ENV=development
   APP_DEBUG=true
   APP_URL=http://localhost/clothy/public

   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=clothy_outlet
   DB_USER=root
   DB_PASS=your_password
   ```

5. **Set up Apache virtual host** (or use XAMPP)
   - Point your web server to the `public/` directory
   - Ensure `mod_rewrite` is enabled

6. **Access the application**
   - Frontend: `http://localhost/clothy/public`
   - Admin Panel: `http://localhost/clothy/public/admin`

---

## 🔑 Default Credentials

### Admin Login

- **Email:** `admin@clothyoutlet.com`
- **Password:** `Admin@1234`

> ⚠️ **Important:** Change these credentials immediately in production!

---

## 📁 Project Structure

```
clothy/
├── app/
│   ├── controllers/      # Application controllers
│   │   └── Admin/        # Admin panel controllers
│   ├── middleware/       # Request middleware
│   ├── models/           # Database models
│   └── views/            # View templates
│       ├── admin/        # Admin panel views
│       ├── auth/         # Authentication views
│       ├── errors/       # Error pages
│       └── partials/     # Reusable components
├── config/
│   ├── config.php        # Application configuration
│   └── routes.php        # Route definitions
├── core/                 # Framework core classes
│   ├── Controller.php
│   ├── Database.php
│   ├── EnvLoader.php
│   ├── Mailer.php
│   ├── Model.php
│   ├── Router.php
│   ├── Session.php
│   └── View.php
├── database/
│   ├── clothy_outlet.sql # Database schema
│   └── seed.sql          # Sample data
├── public/               # Web root
│   ├── assets/           # CSS, JS, images
│   ├── .htaccess         # Apache rewrite rules
│   └── index.php         # Application entry point
├── storage/
│   └── logs/             # Application logs
├── uploads/              # User uploads
├── .env.example          # Environment template
├── .gitignore
└── README.md
```

---

## 🖼️ Screenshots

### Homepage

![Homepage](screenshots/homepage.png)

### Product Listing

![Products](screenshots/products.png)

### Admin Dashboard

![Admin Dashboard](screenshots/admin-dashboard.png)

### Shopping Cart

![Cart](screenshots/cart.png)

> 📝 _Screenshots coming soon_

---

## 🚀 Usage

### Customer Flow

1. Browse products on the homepage or by category
2. Add items to cart
3. Create an account or login
4. Proceed to checkout
5. Track your order in your account

### Admin Flow

1. Login at `/admin`
2. View dashboard for sales overview
3. Manage products, categories, and orders
4. View customer information
5. Update order statuses

---

## 🔮 Future Improvements

- [ ] Payment gateway integration (Stripe, PayPal)
- [ ] Email notifications for orders
- [ ] Product reviews and ratings
- [ ] Inventory management with stock alerts
- [ ] Coupon and discount system
- [ ] Multi-language support
- [ ] API endpoints for mobile app
- [ ] Advanced analytics dashboard
- [ ] Social media login (OAuth)
- [ ] Product image gallery with zoom

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👤 Author

**Your Name**

- GitHub: [@yourusername](https://github.com/yourusername)
- LinkedIn: [Your LinkedIn](https://linkedin.com/in/yourprofile)

---

## 🙏 Acknowledgments

- Bootstrap for the UI components
- Font Awesome for icons
- All contributors who help improve this project

---

<p align="center">Made with ❤️ for fashion lovers</p>
