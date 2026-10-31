# Jawai Mumal - Hostinger Deployment & Setup Guide

Welcome to **Jawai Mumal** — Luxury Wilderness Camp, Royal Mewari Dining & Leopard Safari web application with built-in Administrative Management Portal.

Built with **HTML5, Vanilla CSS, PHP 8.x**, and **MySQL / SQL**, tailored specifically for Hostinger Shared & Cloud Hosting (LiteSpeed / Apache).

---

## 🚀 Quick Hostinger Deployment (Step-by-Step)

### Step 1: Upload Files to Hostinger `public_html`
1. Log into your [Hostinger hPanel](https://hpanel.hostinger.com).
2. Go to **Websites** → Select your domain → Click **File Manager**.
3. Open the **`public_html`** directory.
4. Upload all the files and folders from this project into `public_html`:
   - `admin/`
   - `assets/`
   - `includes/`
   - `config.php`
   - `database.sql`
   - `index.php`
   - `rooms.php`
   - `restaurant.php`
   - `safari.php`
   - `book.php`
   - `contact.php`
   - `.htaccess`

---

### Step 2: Create MySQL Database in Hostinger hPanel
1. In Hostinger hPanel, go to **Databases** → **MySQL Databases**.
2. Enter:
   - **Database Name**: e.g. `u123456789_jawai`
   - **MySQL Username**: e.g. `u123456789_admin`
   - **Password**: Create a strong password (copy it).
3. Click **Create**.

---

### Step 3: Import `database.sql` in phpMyAdmin
1. On the same Databases page, click **Enter phpMyAdmin** next to your newly created database.
2. Click on your database name on the left sidebar.
3. Click the **Import** tab at the top.
4. Click **Choose File** and select **`database.sql`**.
5. Scroll down and click **Go** (or **Import**).
6. All 8 tables (`rooms`, `menu_items`, `safaris`, `stay_bookings`, `dining_bookings`, `safari_bookings`, `inquiries`, `admin_users`) along with complete luxury seed data will be imported instantly!

---

### Step 4: Configure `config.php`
Open `config.php` in Hostinger File Manager (or code editor) and enter your Hostinger database details:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'u123456789_admin');     // Your Hostinger MySQL Username
define('DB_PASS', 'YourStrongPassword');   // Your Hostinger MySQL Password
define('DB_NAME', 'u123456789_jawai');     // Your Hostinger Database Name
```

> **Note:** The application also has an intelligent automatic fallback. If MySQL is not configured yet, it will seamlessly initialize an SQLite database so the website and admin panel never crash with a white screen!

---

## 🔐 Administrative Management Portal

- **Admin Login URL**: `https://yourdomain.com/admin/login.php`
- **Default Username**: `admin`
- **Default Password**: `admin123`

*(You can change this password anytime in the Admin Portal under **Resort Settings**).*

---

## 🌟 Key Features Included

### 1. Public Sanctuary Experience
- **Hotel / Luxury Stays (`rooms.php`)**: Handcrafted tent suites, cliff villas, plunge pool amenities, and dynamic live price calculator.
- **Royal Mewari Dining & Digital Menu (`restaurant.php`)**: Signature Laal Maas, Dal Baati Mahathali, bush dinner setups, interactive veg/non-veg filterable digital menu, and table reservation engine.
- **Jawai Leopard Safari (`safari.php`)**: Dawn & Dusk 4x4 open Gypsy safaris, Rabari trackers, Jawai Dam flamingos & crocodiles wetland safari.
- **Universal Booking Engine (`book.php`)**: Generates official royal reservation confirmation vouchers with printable view and 1-click WhatsApp transmission.
- **VIP Inquiries (`contact.php`)**: Full resort buyout inquiries and royal celebration contact forms.

### 2. Comprehensive Admin Panel (`admin/`)
- **Dashboard Overview**: Gross Revenue, active suite bookings, daily safari expeditions, dining covers, and activity timelines.
- **Rooms & Stays Manager**: Room inventory pricing, toggle availability (Available vs Sold Out), check-in / check-out guest status updates.
- **Restaurant & Menu Manager**: Real-time 86'd out-of-stock toggles, add new menu items, table seating and completion status.
- **Safari Operations & Fleet Dispatch**: Assign 4x4 Gypsies (Gypsy #1 to #4) and allocate master trackers to guest departures.
- **VIP Inquiries**: Review and resolve direct messages.
- **Resort Settings**: Manage contact information, tax rates, seasonal discounts, and database backups.

---

## 💻 Local Testing (Before Uploading)

To run this application locally on your computer:
```bash
php -S localhost:8000
```
Open your browser at:
- Public Website: `http://localhost:8000`
- Admin Portal: `http://localhost:8000/admin/login.php`
