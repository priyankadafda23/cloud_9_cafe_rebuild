# ☁️ Cloud 9 Cafe - Online Food Ordering System

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> A modern, responsive web-based food ordering system for cafes and restaurants with user management, order tracking, and reward points system. Uses JSON files as database - no MySQL required!

---

## 📋 Application Overview

**Cloud 9 Cafe** is a full-featured online food ordering platform built with PHP, Bootstrap, and JSON-based data storage. The system allows customers to browse menus, place orders, track order status, and earn reward points. It includes a comprehensive admin panel for managing menu items, orders, and user accounts.

### Key Highlights
- 🍽️ Complete food ordering workflow (Cart → Checkout → Order Tracking)
- 👤 Role-based access (Guest, User, Admin)
- 🎁 Reward points system (+10 points per order)
- 📱 Fully responsive design
- 🔒 Secure **cookie-based** authentication (no PHP sessions)
- 📊 Admin dashboard with order analytics
- 🛒 **AJAX Add to Cart** - Add items without page reload
- 📁 **JSON Database** - No MySQL setup required

---

## ✨ Features

### Customer Features
- **User Authentication** - Register, login with cookie-based auth
- **Browse Menu** - View menu items with categories, prices, and images
- **Shopping Cart** - Add items via AJAX, update quantities, remove items
- **Checkout Process** - Delivery address, order notes, payment method selection
- **Order History** - View all past orders with status tracking
- **Order Tracking** - Real-time status updates (Pending → Preparing → Completed)
- **Reward Points** - Earn 10 reward points for every successful order
- **Wishlist** - Save favorite items for later
- **Profile Management** - Update personal details and addresses

### Admin Features
- **Dashboard** - Overview of orders, users, and revenue statistics
- **Menu Management** - Add, edit, delete menu items with image uploads
- **Order Management** - View, update status, and manage all orders
- **User Management** - View and manage registered customers
- **Message Management** - View contact form submissions
- **Profile Settings** - Admin account management

---

## 🔧 Functions & Modules

### Core Modules

| Module | Description | Location |
|--------|-------------|----------|
| **Authentication** | User/Admin login with cookie-based auth | `auth/` |
| **Database** | JSON-based database operations (JsonDB class) | `config/JsonDB.php` |
| **Token Auth** | Cookie-based authentication system | `config/TokenAuth.php` |
| **Layout Engine** | Three separate layouts (Public, User Dashboard, Admin) | `includes/layout.php`, `includes/dashboard_layout.php`, `admin/admin_layout.php` |
| **Order System** | Cart, checkout, order processing | `user/cart.php`, `user/checkout.php` |
| **Admin Panel** | Backend management interface | `admin/` |
| **API Endpoints** | AJAX handlers (Add to Cart) | `api/` |

### Key Functions (`config/TokenAuth.php`)

```php
$auth->isUserLoggedIn()    // Check if user is logged in
$auth->isAdminLoggedIn()   // Check if admin is logged in
$auth->getUserId()         // Get logged-in user ID
$auth->getUserName()       // Get logged-in user name
$auth->loginUser($id, $name, $role)    // Set user auth cookie
$auth->loginAdmin($id, $name, $role)   // Set admin auth cookie
$auth->logout()            // Clear auth cookie

// JsonDB Functions
$db->insert($table, $data)           // Insert new record
$db->select($table, $where)          // Select records
$db->selectOne($table, $where)       // Select single record
$db->update($table, $data, $where)   // Update records
$db->delete($table, $where)          // Delete records
$db->count($table, $where)           // Count records
```

---

## 📁 Folder Structure

```
cloud_9_cafe_rebuild/
│
├── 📄 index.php                 # Entry point (redirects to pages/index.php)
├── 📄 .env                      # Environment configuration
├── 📄 .env.example              # Environment template
│
├── 📁 admin/                    # Admin panel files
│   ├── dashboard.php            # Admin dashboard
│   ├── menu.php                 # Menu item list
│   ├── menu_add.php             # Add new menu item
│   ├── menu_edit.php            # Edit menu item
│   ├── orders.php               # Manage orders
│   ├── order_view.php           # View order details
│   ├── users.php                # Manage users
│   ├── messages.php             # Contact form messages
│   ├── profile.php              # Admin profile
│   └── admin_layout.php         # Admin layout template
│
├── 📁 api/                      # API endpoints
│   └── add_to_cart.php          # AJAX endpoint for adding to cart
│
├── 📁 auth/                     # Authentication files
│   ├── login.php                # User login (with demo credentials)
│   ├── register.php             # User registration
│   ├── forgot_password.php      # Password reset request
│   ├── reset_password.php       # Password reset form
│   ├── verify_otp.php           # OTP verification
│   └── logout.php               # Logout handler
│
├── 📁 config/                   # Configuration files
│   ├── db_config.php            # Database & Auth initialization
│   ├── JsonDB.php               # JSON database class
│   ├── TokenAuth.php            # Cookie authentication class
│   ├── Env.php                  # Environment loader
│   └── check.php                # System check utility
│
├── 📁 includes/                 # Shared components
│   ├── layout.php               # Main public layout
│   ├── dashboard_layout.php     # User dashboard layout
│   └── functions.php            # Common functions
│
├── 📁 pages/                    # Public pages
│   ├── index.php                # Homepage (with Popular Picks)
│   ├── about.php                # About us
│   ├── contact.php              # Contact form
│   ├── faq.php                  # FAQ page
│   ├── privacy_policy.php       # Privacy policy
│   └── terms_of_service.php     # Terms of service
│
├── 📁 user/                     # User account pages
│   ├── dashboard.php            # User dashboard
│   ├── profile.php              # User profile
│   ├── edit_profile.php         # Edit profile
│   ├── change_password.php      # Change password
│   ├── addresses.php            # Manage addresses
│   ├── cart.php                 # Shopping cart
│   ├── checkout.php             # Checkout process
│   ├── order_success.php        # Order confirmation
│   ├── orders.php               # Order history
│   └── wishlist.php             # Wishlist
│
├── 📁 assets/                   # Static assets
│   ├── css/
│   │   ├── bootstrap*.css       # Bootstrap framework
│   │   ├── theme.css            # Custom theme styles
│   │   └── layout/              # Layout-specific CSS files
│   │       ├── layout.css       # Public layout styles
│   │       ├── dashboard_layout.css  # User dashboard styles
│   │       └── admin_layout.css # Admin dashboard styles
│   ├── js/                      # JavaScript files
│   ├── images/                  # Static images
│   ├── uploads/                 # User uploads (menu images, profiles)
│   └── fontawesome/             # Font Awesome icons
│
├── 📁 data/                     # JSON database files (auto-created)
│   ├── cafe_users.json          # User accounts
│   ├── cafe_admins.json         # Admin accounts
│   ├── menu_items.json          # Menu items
│   ├── cafe_cart.json           # Shopping cart
│   ├── cafe_orders.json         # Orders
│   ├── cafe_order_items.json    # Order items
│   ├── cafe_offers.json         # Promotional offers
│   ├── user_addresses.json      # User addresses
│   └── contact_messages.json    # Contact messages
│
└── 📄 ORDER_SYSTEM.md           # Detailed order workflow documentation
```

---

## ⚙️ Installation & Configuration

### Prerequisites
- PHP 8.0 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Installation Steps

1. **Clone or download the repository**
   ```bash
   git clone <repository-url>
   cd cloud_9_cafe_rebuild
   ```

2. **No database setup required!**
   - JSON database files are auto-created in `/data/` folder
   - No MySQL, no SQL imports needed

3. **Configure environment variables** (optional)
   ```bash
   copy .env.example .env
   ```
   Edit `.env` file with your settings:
   ```ini
   APP_NAME="Cloud 9 Cafe"
   APP_URL=http://localhost/cloud_9_cafe_rebuild
   APP_TIMEZONE=Asia/Kolkata
   ```

4. **Set up web server**
   - Point your web server to the project root directory
   - Ensure PHP has write permissions for the `data/` folder

5. **Default Credentials** (auto-created on first run)

   | Account Type | Email | Password | Role |
   |--------------|-------|----------|------|
   | **Admin** | `admin@cloud9cafe.com` | `admin123` | super_admin |
   | **User** | `user@cloud9cafe.com` | `user123` | User |

---

## 🚀 How To Use

### For Customers

1. **Browse Menu**
   - Visit the homepage to see featured items in "Popular Picks"
   - Click "Add to Cart" on any item
   - If not logged in, you'll be redirected to login page

2. **Create Account**
   - Click "Register" to create a new account
   - Or use the demo user: `user@cloud9cafe.com` / `user123`

3. **Place Order**
   - Add items to cart (AJAX - no page reload!)
   - Go to cart and click "Checkout"
   - Enter delivery address and payment method
   - Confirm order

4. **Track Orders**
   - Go to "My Orders" in your dashboard
   - View order status and history

5. **Earn Rewards**
   - Get 10 reward points for every completed order
   - View points balance in dashboard

### For Administrators

1. **Access Admin Panel**
   - Navigate to `/auth/login.php`
   - Login with admin credentials: `admin@cloud9cafe.com` / `admin123`

2. **Manage Menu**
   - Go to "Menu" section
   - Add, edit, or delete menu items
   - Upload item images

3. **Process Orders**
   - Go to "Orders" section
   - View new orders and update status
   - Track order fulfillment

4. **Manage Users**
   - View registered customers
   - Manage user accounts

---

## 👥 User Roles

| Role | Description | Permissions |
|------|-------------|-------------|
| **Guest** | Unauthenticated visitor | Browse menu, view pages, must login to order |
| **Customer** | Registered user | Full ordering capabilities, order history, reward points |
| **Admin** | System administrator | Full access to admin panel, menu/order/user management |

---

## 🗄️ Database Schema

### JSON-Based Database Tables

#### `cafe_users` - Customer Accounts
```json
{
  "id": 1,
  "fullname": "Demo User",
  "email": "user@cloud9cafe.com",
  "password": "user123",
  "mobile": "9876543211",
  "address": "123 Coffee Street",
  "role": "User",
  "status": "Active",
  "reward_points": 50,
  "profile_picture": "",
  "created_at": "2024-01-01 10:00:00",
  "updated_at": "2024-01-01 10:00:00"
}
```

#### `cafe_admins` - Admin Accounts
```json
{
  "id": 1,
  "fullname": "Admin User",
  "email": "admin@cloud9cafe.com",
  "password": "admin123",
  "mobile": "9876543210",
  "role": "super_admin",
  "status": "Active",
  "created_at": "2024-01-01 10:00:00",
  "updated_at": "2024-01-01 10:00:00"
}
```

#### `menu_items` - Menu Items
```json
{
  "id": 1,
  "name": "Caramel Macchiato",
  "description": "Rich espresso with caramel",
  "price": 450,
  "category": "Coffee",
  "image": "images/menu/coffee.jpg",
  "stock_quantity": 100,
  "availability": "Available",
  "featured": 1,
  "created_at": "2024-01-01 10:00:00",
  "updated_at": "2024-01-01 10:00:00"
}
```

#### `cafe_cart` - Shopping Cart
```json
{
  "id": 1,
  "user_id": 1,
  "menu_item_id": 1,
  "quantity": 2,
  "customization": "Extra shot",
  "created_at": "2024-01-01 12:00:00",
  "updated_at": "2024-01-01 12:00:00"
}
```

#### `cafe_orders` - Orders
```json
{
  "id": 1,
  "order_number": "ORD-20240101-1234",
  "user_id": 1,
  "total_amount": 900.00,
  "order_note": "Deliver by 7 PM",
  "status": "Pending",
  "payment_status": "Pending",
  "payment_method": "Cash",
  "delivery_address": "123 Coffee Street",
  "order_date": "2024-01-01 12:30:00",
  "created_at": "2024-01-01 12:30:00",
  "updated_at": "2024-01-01 12:30:00"
}
```

#### `cafe_order_items` - Order Line Items
```json
{
  "id": 1,
  "order_id": 1,
  "menu_item_id": 1,
  "quantity": 2,
  "unit_price": 450.00,
  "subtotal": 900.00,
  "customization": "Extra shot",
  "created_at": "2024-01-01 12:30:00"
}
```

#### `cafe_offers` - Promotional Offers
```json
{
  "id": 1,
  "title": "Summer Special",
  "description": "20% off all cold drinks",
  "discount_percentage": 20,
  "start_date": "2024-06-01",
  "end_date": "2024-08-31",
  "status": "Active",
  "created_at": "2024-01-01 10:00:00"
}
```

---

## 🔒 Security Features

- **Cookie-based Authentication** - Secure signed cookies with HMAC-SHA256 (no PHP sessions)
- **Token Signing** - All auth tokens are cryptographically signed
- **HTTP-Only Cookies** - Prevents XSS attacks
- **SameSite Cookies** - CSRF protection
- **Input Sanitization** - All user inputs sanitized
- **Role Verification** - Admin and user role checks on all protected pages
- **File Upload Validation** - Image type and size validation for uploads
- **XSS Protection** - Output encoding with `htmlspecialchars()`

---

## 🛠️ Technologies Used

### Backend
- **PHP 8.0+** - Server-side scripting
- **JsonDB** - Custom JSON-based database class (no MySQL!)
- **TokenAuth** - Custom cookie-based authentication

### Frontend
- **HTML5** - Markup language
- **CSS3** - Styling
- **Bootstrap 5.3** - CSS framework
- **JavaScript** - Client-side scripting
- **Font Awesome** - Icons

### Data Storage
- **JSON Files** - Lightweight data storage in `/data/` folder

---

## 📸 Screenshots

> *Screenshots to be added here*

| Page | Preview |
|------|---------|
| Homepage | ![Homepage](screenshots/homepage.png) |
| Menu | ![Menu](screenshots/menu.png) |
| Cart | ![Cart](screenshots/cart.png) |
| Checkout | ![Checkout](screenshots/checkout.png) |
| Admin Dashboard | ![Admin](screenshots/admin.png) |

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Comment complex logic
- Maintain consistent indentation (4 spaces)

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2024 Cloud 9 Cafe

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
```

---

## 🆘 Support

### Getting Help

If you encounter any issues or have questions:

1. **Check Documentation** - Review this README and ORDER_SYSTEM.md
2. **Search Issues** - Check existing GitHub issues
3. **Create Issue** - Open a new issue with:
   - Clear description of the problem
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots (if applicable)

### Contact

- 📧 Email: support@cloud9cafe.com
- 🌐 Website: [www.cloud9cafe.com](http://www.cloud9cafe.com)
- 💬 GitHub Discussions: [github.com/cloud9cafe/discussions](https://github.com)

---

## 🙏 Acknowledgments

- Bootstrap Team for the amazing CSS framework
- Font Awesome for the icon library
- All contributors who helped improve this project

---

<p align="center">
  Made with ❤️ by Cloud 9 Cafe Team
  <br>
  <small>☁️ Elevating Your Dining Experience ☁️</small>
</p>
