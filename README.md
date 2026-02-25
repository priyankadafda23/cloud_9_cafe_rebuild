# ☕ Cloud 9 Cafe - Cafe Management System

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/FontAwesome-6.4-528DD7?style=for-the-badge&logo=fontawesome&logoColor=white" alt="FontAwesome">
</p>

<p align="center">
  <b>A complete Core PHP-based Cafe Management System with modern UI</b>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#installation--configuration">Installation</a> •
  <a href="#how-to-use">How To Use</a> •
  <a href="#folder-structure">Folder Structure</a> •
  <a href="#technologies-used">Technologies</a>
</p>

---

## 📋 Table of Contents

1. [Application Overview](#-application-overview)
2. [Features](#-features)
3. [Functions & Modules](#-functions--modules)
4. [Folder Structure](#-folder-structure)
5. [Installation & Configuration](#-installation--configuration)
6. [How To Use](#-how-to-use)
7. [User Roles](#-user-roles)
8. [Database Schema](#-database-schema)
9. [Security Features](#-security-features)
10. [Technologies Used](#-technologies-used)
11. [Screenshots](#-screenshots)
12. [Contributing](#-contributing)
13. [License](#-license)
14. [Support](#-support)

---

## 📖 Application Overview

**Cloud 9 Cafe** is a comprehensive web-based Cafe Management System built with Core PHP. It provides a complete solution for managing a cafe business online, including customer ordering, menu management, admin dashboard, and loyalty rewards program.

### Key Highlights
- 🎨 **Modern UI/UX** - Built with CSS3 variables, Bootstrap 5, and custom animations
- 📱 **Fully Responsive** - Mobile-first design approach
- 🔒 **Secure** - Session-based authentication with CSRF protection
- ⚡ **Fast** - Optimized database queries and CDN assets
- 🎯 **Feature Rich** - Complete order management, cart system, and admin panel
- 🇮🇳 **Indian Rupee Support** - All prices displayed in ₹ (Rupees)

---

## ✨ Features

### 🌟 Public Features
| Feature | Description |
|---------|-------------|
| **Home Page** | Hero section with stats, featured products, testimonials |
| **Menu Browsing** | Category filter, search, product cards with hover effects |
| **Shopping Cart** | Add/remove items, quantity control, real-time total |
| **User Registration** | Account creation with profile picture upload |
| **User Login** | Secure authentication with session management |
| **Contact Form** | Customer inquiry submission with admin notification |
| **About Page** | Cafe information, story, and team members |

### 👤 User Dashboard Features
| Feature | Description |
|---------|-------------|
| **Dashboard** | Order statistics, reward points, recent orders |
| **Profile Management** | Edit profile, upload avatar, update info |
| **Order History** | View all orders with status tracking |
| **Addresses** | Manage multiple delivery addresses |
| **Cart Management** | Full cart control before checkout |
| **Wishlist** | Save favorite items for later |
| **Password Change** | Secure password update functionality |

### 🔧 Admin Panel Features
| Feature | Description |
|---------|-------------|
| **Admin Dashboard** | Statistics cards, recent orders, recent users |
| **User Management** | View users, toggle status, delete accounts |
| **Menu Management** | Add/edit/delete items, toggle availability, featured items |
| **Order Management** | View orders, update status (Pending → Delivered), payment tracking |
| **Message Inbox** | View and reply to contact form submissions |
| **Profile Settings** | Admin profile management |

---

## 🔧 Functions & Modules

### Core Functions (`includes/functions.php`)

```php
// Authentication
isLoggedIn()              // Check if user is logged in
getCurrentUserId()        // Get logged in user ID
getCurrentUserName()      // Get logged in user name
requireLogin()            // Redirect if not logged in

// Utilities
formatPrice($price)       // Format price as ₹XX.XX (Rupees)
generateOrderNumber()     // Generate unique order ID
sanitize($data)           // Clean user input
setFlashMessage($type, $message)  // Set session flash message
getFlashMessage()         // Get and clear flash message
```

### Environment Module (`config/Env.php`)

```php
Env::get('KEY', 'default')      // Get string value
Env::getBool('KEY', false)      // Get boolean value
Env::getInt('KEY', 0)           // Get integer value
Env::has('KEY')                 // Check if exists
```

### Database Module (`config/db_config.php`)

- Automatic database connection
- Character set configuration (utf8mb4)
- Environment-based credentials
- Connection error handling

---

## 📁 Folder Structure

```
cloud_9_cafe_rebuild/
│
├── 📄 Root Files
│   ├── .env                          # Environment configuration
│   ├── .env.example                  # Example environment file
│   ├── .gitignore                    # Git ignore rules
│   ├── index.php                     # Entry point (redirects to pages/)
│   ├── README.md                     # This documentation
│   └── ORDER_SYSTEM.md               # Order flow documentation
│
├── 🎨 assets/                        # Public assets
│   ├── css/
│   │   ├── theme.css                 # Global theme variables & styles
│   │   └── bootstrap*.css            # Bootstrap framework
│   ├── js/
│   │   ├── theme.js                  # UI enhancements & toast system
│   │   ├── bootstrap*.js             # Bootstrap JS
│   │   └── jquery.js                 # jQuery library
│   ├── fontawesome/                  # FontAwesome icons
│   └── uploads/                      # User uploaded files
│       ├── Profile/                  # Profile pictures
│       │   └── {username}_{id}/
│       │       └── profile_picture.jpg
│       └── menu_images/              # Menu item images
│           └── {menu_id}/
│               └── image1.jpg
│
├── ⚙️ config/                        # Configuration files
│   ├── db_config.php                 # Database connection
│   ├── Env.php                       # Environment loader class
│   └── Config.php                    # Configuration helper
│
├── 🗄️ database/                      # Database files
│   ├── schema.sql                    # Complete database schema
│   └── install_database.php          # Web-based installer
│
├── 🧩 includes/                      # Shared components
│   ├── layout.php                    # Main public layout (modern)
│   ├── dashboard_layout.php          # User dashboard layout (modern)
│   └── functions.php                 # Common functions
│
├── 🌐 pages/                         # Public pages
│   ├── index.php                     # Home page
│   ├── about.php                     # About us page
│   ├── contact.php                   # Contact page
│   ├── faq.php                       # FAQ page
│   ├── privacy_policy.php            # Privacy policy
│   ├── terms_of_service.php          # Terms of service
│   └── menu/
│       ├── menu.php                  # Menu page
│       └── menu_item_detail.php      # Menu item detail
│
├── 🔐 auth/                          # Authentication
│   ├── login.php                     # Login page
│   ├── register.php                  # Registration page
│   ├── forgot_password.php           # Forgot password
│   ├── reset_password.php            # Reset password
│   ├── verify_otp.php                # OTP verification
│   └── logout.php                    # Logout handler
│
├── 👤 user/                          # User dashboard
│   ├── dashboard.php                 # User dashboard
│   ├── profile.php                   # View profile
│   ├── edit_profile.php              # Edit profile
│   ├── orders.php                    # Order history
│   ├── cart.php                      # Shopping cart
│   ├── wishlist.php                  # Wishlist
│   ├── addresses.php                 # Manage addresses
│   ├── checkout.php                  # Checkout process
│   ├── order_success.php             # Order confirmation
│   └── change_password.php           # Change password
│
├── 🔧 admin/                         # Admin panel
│   ├── admin_layout.php              # Admin layout (dark sidebar)
│   ├── dashboard.php                 # Admin dashboard
│   ├── users.php                     # User management
│   ├── menu.php                      # Menu management
│   ├── menu_add.php                  # Add menu item
│   ├── menu_edit.php                 # Edit menu item
│   ├── orders.php                    # Order management
│   ├── order_view.php                # View order details
│   ├── messages.php                  # Contact messages
│   └── profile.php                   # Admin profile
│
└── 📦 vendor/                        # Third-party libraries
    └── PHPMailer/                    # Email sending library
```

---

## 🚀 Installation & Configuration

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (for local development)

### Step-by-Step Installation

#### 1. Clone/Download Project
```bash
# Clone to your web root
cd C:\xampp\htdocs\
git clone <repository-url> cloud_9_cafe_rebuild
```

#### 2. Create Database
```sql
-- Create database
CREATE DATABASE cloud_9_cafe_rebuild CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 3. Configure Environment
```bash
# Copy example file
cp .env.example .env

# Edit .env with your settings
```

**`.env` Configuration:**
```ini
# Application Settings
APP_NAME="Cloud 9 Cafe"
APP_ENV=development
APP_URL=http://localhost/cloud_9_cafe_rebuild

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cloud_9_cafe_rebuild
DB_USERNAME=root
DB_PASSWORD=

# Admin Credentials (Change before production!)
ADMIN_EMAIL=admin@cloud9cafe.com
ADMIN_PASSWORD=admin123

# Email Configuration (Optional)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
```

#### 4. Install Database
**Option A: Web Installer (Recommended)**
```
http://localhost/cloud_9_cafe_rebuild/database/install_database.php
```

**Option B: Manual Import**
```
Import database/schema.sql via phpMyAdmin
```

#### 5. Create Upload Folders
```bash
# Create necessary folders for uploads
mkdir -p assets/uploads/Profile
mkdir -p assets/uploads/menu_images
```

#### 6. Access Application
```
Public Site: http://localhost/cloud_9_cafe_rebuild/pages/index.php
Admin Panel: http://localhost/cloud_9_cafe_rebuild/admin/dashboard.php
```

---

## 📖 How To Use

### 👥 For Customers

1. **Browse Menu**
   - Visit the menu page
   - Filter by category (Coffee, Snack, Dessert)
   - Search for specific items
   - Click "Add to Cart"

2. **Create Account**
   - Click "Register" in navbar
   - Fill in details with profile picture
   - Submit form

3. **Place Order**
   - Add items to cart
   - Review cart and proceed to checkout
   - Confirm order

4. **Track Orders**
   - Login to dashboard
   - View "My Orders" section
   - Check order status (Pending → Preparing → Ready → Delivered)

5. **Earn Rewards**
   - Earn 10 points per ₹100 spent
   - Use points for discounts on future orders

### 🔧 For Administrators

1. **Login to Admin Panel**
   ```
   URL: /admin/dashboard.php
   Email: admin@cloud9cafe.com
   Password: admin123 (change this!)
   ```

2. **Manage Menu**
   - Add new items with images
   - Set categories, prices (in ₹), stock
   - Mark items as "Featured"
   - Toggle availability

3. **Process Orders**
   - View new orders in dashboard
   - Update order status
   - Track payment status
   - View order details

4. **Manage Users**
   - View all registered users
   - Toggle user status (Active/Inactive)
   - Delete accounts if needed

---

## 👤 User Roles

### Role Permissions

| Feature | Guest | Customer | Admin |
|---------|-------|----------|-------|
| Browse Menu | ✅ | ✅ | ✅ |
| Add to Cart | ❌ | ✅ | ✅ |
| Place Order | ❌ | ✅ | ✅ |
| Track Orders | ❌ | ✅ | ✅ |
| Manage Profile | ❌ | ✅ | ✅ |
| Manage Menu | ❌ | ❌ | ✅ |
| Manage Orders | ❌ | ❌ | ✅ |
| Manage Users | ❌ | ❌ | ✅ |
| View Messages | ❌ | ❌ | ✅ |

### Session Variables

```php
// Customer Session
$_SESSION['cafe_user_id']      // User ID
$_SESSION['cafe_user_name']    // User full name

// Admin Session
$_SESSION['cafe_admin_id']     // Admin ID
$_SESSION['cafe_admin_name']   // Admin name
$_SESSION['cafe_admin_role']   // Role (super_admin/manager/staff)
```

---

## 🗄️ Database Schema

### Tables Overview

| Table | Purpose | Records |
|-------|---------|---------|
| `cafe_users` | Customer accounts | Users data with reward points |
| `cafe_admins` | Admin accounts | Admin/staff login credentials |
| `menu_items` | Menu products | Coffee, Snacks, Desserts |
| `cafe_orders` | Orders | Order headers with status |
| `cafe_order_items` | Order details | Items in each order |
| `cafe_cart` | Shopping cart | User's cart items |
| `user_addresses` | User addresses | Multiple addresses per user |
| `contact_messages` | Inquiries | Contact form submissions |

### Entity Relationship Diagram

```
cafe_users ||--o{ cafe_orders : places
cafe_users ||--o{ cafe_cart : has
cafe_users ||--o{ user_addresses : has
cafe_orders ||--|{ cafe_order_items : contains
menu_items ||--o{ cafe_order_items : includes
menu_items ||--o{ cafe_cart : in
```

### File Upload Structure

**Profile Pictures:**
```
assets/uploads/Profile/{username}_{user_id}/profile_picture.{ext}
Example: assets/uploads/Profile/John_Doe_1/profile_picture.jpg
```

**Menu Images:**
```
assets/uploads/menu_images/{menu_id}/image1.{ext}
Example: assets/uploads/menu_images/5/image1.png
```

---

## 🔒 Security Features

| Feature | Implementation |
|---------|---------------|
| **Environment Variables** | Sensitive data in `.env` file (not committed) |
| **Session Security** | Secure session handling with timeout |
| **SQL Injection Prevention** | Prepared statements for all queries |
| **XSS Protection** | Output escaping with `htmlspecialchars()` |
| **CSRF Protection** | Token-based CSRF validation ready |
| **File Upload** | Type and size validation |
| **Authentication** | Session-based with role checking |

### Security Best Practices
- ✅ Never commit `.env` file
- ✅ Change default admin password before production
- ✅ Set `APP_ENV=production` in production
- ✅ Use HTTPS in production
- ✅ Regular database backups
- ✅ Input sanitization on all forms

---

## 💻 Technologies Used

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 7.4+ | Server-side scripting |
| MySQL | 5.7+ | Database |
| Apache | 2.4+ | Web server |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| HTML5 | - | Markup |
| CSS3 | - | Styling with variables |
| JavaScript | ES6+ | Interactivity |
| Bootstrap | 5.3.2 | CSS Framework |
| FontAwesome | 6.4.2 | Icons |
| Poppins | - | Google Font |

### Libraries & Tools
| Tool | Purpose |
|------|---------|
| PHPMailer | Email sending |
| Git | Version control |
| XAMPP | Local development |

---

## 📸 Screenshots

### Public Pages
```
[Home Page]
┌─────────────────────────────────────────┐
│  ☕ Cloud 9 Cafe    [Home][Menu][Login] │  ← Sticky Navbar
├─────────────────────────────────────────┤
│                                         │
│   Experience the Perfect Cup            │  ← Hero Section
│   of Coffee                             │
│                                         │
│   [Explore Menu] [Learn More]           │
│                                         │
│   15K+      50+      4.9                │  ← Stats
│   Happy     Menu     Rating             │
│   Customers Items                       │
│                                         │
├─────────────────────────────────────────┤
│   ☕ Premium Beans   🔥 Freshly Roasted │  ← Features
│   🚚 Fast Delivery                      │
├─────────────────────────────────────────┤
│   Popular Picks        [View All →]     │  ← Products
│   ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐     │
│   │ ☕  │ │ ☕  │ │ 🥐  │ │ 🍰  │     │
│   │₹450│ │₹380│ │₹280│ │₹420│     │
│   └─────┘ └─────┘ └─────┘ └─────┘     │
└─────────────────────────────────────────┘
```

### User Dashboard
```
[User Dashboard]
┌─────────────────────────────────────────┐
│  ☕ Cloud 9 Cafe          🔔 👤 Logout  │
├──────────┬──────────────────────────────┤
│          │  Welcome back, John! ☕      │
│  Profile │  ─────────────────────────   │
│  My      │  ┌────┐ ┌────┐ ┌────┐ ┌────┐│
│  Orders  │  │ 12 │ │ 2  │ │150 │ │ 3  ││  ← Stats Cards
│  Cart    │  │Orders│ │Pending│ │Points│ │Cart │
│  Favorites│ └────┘ └────┘ └────┘ └────┘│
│          │  ─────────────────────────   │
│  ────────│  Recent Orders               │
│  Logout  │  ┌────────────────────────┐  │
│          │  │ Cappuccino  Pending ₹380│  │
│          │  │ Croissant   Delivered₹280│  │
│          │  └────────────────────────┘  │
└──────────┴──────────────────────────────┘
```

### Admin Panel
```
[Admin Dashboard]
┌─────────────────────────────────────────┐
│ ☕ Admin    Dashboard  Users  Menu  ▼   │
├──────────┬──────────────────────────────┤
│ ☕       │  Dashboard                   │
│ Cloud 9  │  ─────────────────────────   │
│ Cafe     │  ┌────┐ ┌────┐ ┌────┐ ┌────┐│
│          │  │ 156│ │ 48 │ │₹2.5L│ │ 52 ││  ← Stats
│ Dashboard│  │Users │ │Orders│ │Revenue│ │Items│
│ Users    │  └────┘ └────┘ └────┘ └────┘│
│ Menu     │  ─────────────────────────   │
│ Orders   │  Recent Orders        [All]  │
│ Messages │  ORD-001  John  Pending ₹450 │
│          │  ORD-002  Jane  Delivered₹280│
│ Profile  │                              │
│          │  Quick Actions               │
│ ────────│  [+ Add Menu Item]           │
│ Logout   │  [Manage Orders]             │
└──────────┴──────────────────────────────┘
```

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

### Development Workflow

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/new-feature
   ```
3. **Make your changes**
   - Follow existing code style
   - Add comments for complex logic
   - Update documentation
4. **Test thoroughly**
   ```bash
   php -l your-file.php
   ```
5. **Commit with clear messages**
   ```bash
   git commit -m "Add: New feature description"
   ```
6. **Push and create Pull Request**

### Coding Standards
- PHP: PSR-12 style guide
- CSS: BEM naming convention
- JavaScript: ESLint recommended
- Database: snake_case for columns

### Commit Message Format
```
Type: Short description

Longer explanation if needed

- Bullet points for changes
- Another change

Fixes #123
```

**Types:**
- `Add:` - New feature
- `Fix:` - Bug fix
- `Update:` - Modification
- `Refactor:` - Code restructuring
- `Docs:` - Documentation

---

## 📄 License

This project is licensed under the **MIT License**.

```
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

| Resource | Link |
|----------|------|
| 📧 Email Support | support@cloud9cafe.com |
| 🐛 Report Bug | GitHub Issues |
| 💡 Request Feature | GitHub Discussions |
| 📖 Documentation | This README |

### Common Issues

**Q: Database connection failed?**
```
A: Check .env file credentials and ensure MySQL is running
```

**Q: Images not uploading?**
```
A: Check folder permissions (755) and ensure assets/uploads/ exists
```

**Q: Emails not sending?**
```
A: Configure SMTP settings in .env with valid credentials
```

**Q: Session expired quickly?**
```
A: Adjust SESSION_LIFETIME in .env (in minutes)
```

### System Requirements Check

```bash
# PHP Version
php -v  # Should be 7.4+

# MySQL Version
mysql --version  # Should be 5.7+

# Apache Modules
apachectl -M  # Should include mod_rewrite
```

---

## 🙏 Acknowledgments

- Bootstrap Team for the amazing CSS framework
- FontAwesome for the beautiful icons
- Google Fonts for Poppins typography
- PHPMailer contributors
- All open-source contributors

---

<p align="center">
  <b>Made with ☕ for Coffee Lovers</b>
</p>

<p align="center">
  ☕ Cloud 9 Cafe - Brewed with Passion
</p>
