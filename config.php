<?php
/**
 * Jawai Mumal - Configuration File
 * Optimized for Hostinger (hPanel / cPanel) and Local Development
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------
// Database Settings (Update these for your Hostinger MySQL Database)
// -------------------------------------------------------------
// In Hostinger hPanel -> Databases -> MySQL Databases:
// Create a new database and user, then paste details below:
define('DB_HOST', 'localhost');
define('DB_USER', 'root');             // Hostinger MySQL Username (e.g. u123456789_jawai)
define('DB_PASS', '');                 // Hostinger MySQL Password
define('DB_NAME', 'jawai_mumal');       // Hostinger Database Name (e.g. u123456789_jawai)
define('DB_PORT', 3306);

// -------------------------------------------------------------
// Resort Information & Defaults
// -------------------------------------------------------------
define('SITE_NAME', 'Jawai Mumal');
define('SITE_TAGLINE', 'Luxury Hotel, Royal Mewari Restaurant & Leopard Safari');
define('RESORT_PHONE', '+91 98290 55555');
define('RESORT_WHATSAPP', '919829055555');
define('RESORT_EMAIL', 'reservations@jawaimumal.com');
define('RESORT_ADDRESS', 'Sena Granite Hills, Near Jawai Dam, Sumerpur, District Pali, Rajasthan 306126, India');
define('CURRENCY_SYMBOL', '₹');

// Admin Credentials (default credentials, can be changed via admin panel)
define('DEFAULT_ADMIN_USER', 'admin');
define('DEFAULT_ADMIN_PASS', 'admin123'); // Change after first login
