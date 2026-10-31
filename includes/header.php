<?php
/**
 * Public Header Component for Jawai Mumal
 */
require_once __DIR__ . '/db.php';

// Fetch active rooms & safaris for navigation or quick select
$navRooms = $pdo->query("SELECT id, name, price FROM rooms WHERE status='available' ORDER BY price ASC")->fetchAll();
$navSafaris = $pdo->query("SELECT id, title, price_per_person, private_gypsy_price FROM safaris WHERE status='active' ORDER BY id ASC")->fetchAll();

$currentUri = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . SITE_NAME : SITE_NAME . ' | ' . SITE_TAGLINE; ?></title>
  <meta name="description" content="Jawai Mumal offers ultra-luxury tented suites, authentic royal Rajasthani dining, and private 4x4 leopard safaris amidst the granite hills of Jawai, Rajasthan.">
  <meta name="keywords" content="Jawai Mumal, Jawai Leopard Safari, Luxury Tent Jawai, Rajasthan Resort, Mewari Dining, Jawai Dam">
  
  <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800;900&family=Playfair+Display:ital,wght@0,500;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Top Announcement Bar -->
  <div class="top-bar">
    <div class="container top-bar-content">
      <div class="top-bar-item">
        <span>📍 <?php echo RESORT_ADDRESS; ?></span>
      </div>
      <div class="top-bar-links">
        <a href="tel:<?php echo str_replace(' ', '', RESORT_PHONE); ?>" class="top-bar-item">
          <span>📞 <?php echo RESORT_PHONE; ?></span>
        </a>
        <a href="mailto:<?php echo RESORT_EMAIL; ?>" class="top-bar-item">
          <span>✉️ <?php echo RESORT_EMAIL; ?></span>
        </a>
        <a href="admin/login.php" class="admin-link-pill">
          <span>🔐 Admin Panel</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Primary Navigation -->
  <header class="navbar" id="navbar">
    <div class="container nav-container">
      <a href="index.php" class="nav-brand">
        <img src="assets/images/favicon.svg" alt="Jawai Mumal Emblem" class="nav-logo-icon">
        <div class="nav-brand-text">
          <span class="nav-brand-title">JAWAI MUMAL</span>
          <span class="nav-brand-subtitle">Hotel • Restro • Jawai Safari</span>
        </div>
      </a>

      <!-- Desktop & Mobile Navigation Links -->
      <nav>
        <ul class="nav-menu" id="navMenu">
          <li><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
          <li><a href="rooms.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'rooms.php' ? 'active' : ''; ?>">Hotel Stays</a></li>
          <li><a href="restaurant.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'restaurant.php' ? 'active' : ''; ?>">Restro & Dining</a></li>
          <li><a href="safari.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'safari.php' ? 'active' : ''; ?>">Jawai Safari</a></li>
          <li><a href="index.php#story" class="nav-link">Our Heritage</a></li>
          <li><a href="index.php#contact" class="nav-link">Contact</a></li>
          <li class="mobile-only" style="margin-top: 1rem;">
            <button class="btn btn-gold btn-sm" onclick="openModal('stayBookingModal')">Reserve A Stay</button>
          </li>
        </ul>
      </nav>

      <div class="nav-actions">
        <button class="btn btn-gold btn-sm" onclick="openModal('stayBookingModal')">
          <span>Book Stay</span>
        </button>
        <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle navigation">
          ☰
        </button>
      </div>
    </div>
  </header>
