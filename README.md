<div align="center">
  <img src="/logo.png" width="120" alt="Billcraft Logo">
  <h1>💎 Billcraft - Premium Business Management System</h1>
</div>

[![GitHub Stars](https://img.shields.io/github/stars/KAJUKATLIii/billcraft?style=for-the-badge)](https://github.com/KAJUKATLIii/billcraft)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-success?style=for-the-badge)](LICENSE)

**Billcraft** is a modern, high-performance Business Management System designed for small to medium enterprises. It streamlines operations, manages inventory, tracks sales, and generates professional PDF invoices with a premium, glassmorphic user interface.

---

## ✨ Key Features

- **🚀 Instant AJAX Operations**: Experience seamless data management. Deleting records (Customers, Products, Vendors) is instant with smooth fade-out animations and zero page reloads.
- **🎨 Premium Glassmorphic UI**: A state-of-the-art interface featuring vibrant gradients, dark mode support, and micro-animations for a "Wow" factor.
- **📦 Inventory Management**: Track products, stock levels, and categories with real-time stock status indicators (Low Stock/Out of Stock).
- **👥 CRM & Vendor Management**: Efficiently manage your customer base and supply chain with dedicated modules.
- **📄 Professional Invoicing**: Generate clean, professional PDF-ready invoices instantly after every sale.
- **💬 WhatsApp Integration**: Send invoices directly to customers via WhatsApp with a single click.
- **📈 Advanced Dashboard**: A bird's-eye view of your business health with real-time statistics and revenue charts.

---

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3 (Vanilla), JavaScript (ES6+), jQuery
- **Backend**: PHP 8.0+
- **Database**: MySQL / MariaDB
- **Styling**: Custom CSS (Glassmorphism), FontAwesome 6, Google Fonts (Plus Jakarta Sans)
- **Security**: Prepared Statements (PDO/MySQLi), Password Hashing (Bcrypt)

---

## 📸 Preview

<div align="center">
  <img src="/dashboard.png" width="45%" alt="Dashboard">
  <img src="/products.png" width="45%" alt="Products Management">
</div>

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.0 or higher
- MySQL Server
- Web Server (Apache/Nginx)

### Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/KAJUKATLIii/billcraft.git
   cd billcraft
   ```

2. **Setup Database**
   - Create a database named `billcraft` (or as specified in `billcraft.sql`).
   - Import the `billcraft.sql` file into your MySQL server.

3. **Configure Connection**
   - Open `connection.php` and update your database credentials:
   ```php
   $con = mysqli_connect("localhost", "your_username", "your_password", "water-managment");
   ```

4. **Run Application**
   - Move the project to your web server root (e.g., `htdocs` or `var/www/html`).
   - Access via `http://localhost/billcraft`

---

## 🔒 Security Note

All database interactions use **Prepared Statements** to prevent SQL injection. Authentication is handled via secure session management and Bcrypt password hashing.

---

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

<div align="center">
  Made with ❤️ by <a href="https://github.com/KAJUKATLIii/">KAJUKATLIii</a>
</div>
