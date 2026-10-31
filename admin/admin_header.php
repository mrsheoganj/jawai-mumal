<?php
/**
 * Admin Layout Header & Sidebar
 */
require_once __DIR__ . '/auth.php';

// Fetch quick badge counters
$stayCount = $pdo->query("SELECT COUNT(*) FROM stay_bookings WHERE status != 'Cancelled'")->fetchColumn();
$dineCount = $pdo->query("SELECT COUNT(*) FROM dining_bookings WHERE status != 'Cancelled'")->fetchColumn();
$safariCount = $pdo->query("SELECT COUNT(*) FROM safari_bookings WHERE status != 'Cancelled'")->fetchColumn();
$inqCount = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'Pending'")->fetchColumn();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Admin Portal' : 'Admin Portal | ' . SITE_NAME; ?></title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

  <!-- Admin Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-header">
      <img src="../assets/images/favicon.svg" alt="Jawai Mumal">
      <div>
        <div class="admin-sidebar-brand">JAWAI MUMAL</div>
        <div class="admin-sidebar-tag">Hotel • Restro • Jawai Safari</div>
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="admin-nav">
      <a href="index.php" class="admin-nav-item <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
        <div class="admin-nav-item-left">
          <span>📊</span>
          <span>Dashboard</span>
        </div>
      </a>

      <a href="rooms.php" class="admin-nav-item <?php echo $currentPage == 'rooms.php' ? 'active' : ''; ?>">
        <div class="admin-nav-item-left">
          <span>🏨</span>
          <span>Hotel & Stays</span>
        </div>
        <span class="admin-badge-count"><?php echo $stayCount; ?></span>
      </a>

      <a href="restaurant.php" class="admin-nav-item <?php echo $currentPage == 'restaurant.php' ? 'active' : ''; ?>">
        <div class="admin-nav-item-left">
          <span>🍽️</span>
          <span>Restro & Menu</span>
        </div>
        <span class="admin-badge-count"><?php echo $dineCount; ?></span>
      </a>

      <a href="safari.php" class="admin-nav-item <?php echo $currentPage == 'safari.php' ? 'active' : ''; ?>">
        <div class="admin-nav-item-left">
          <span>🐆</span>
          <span>Jawai Safari Fleet</span>
        </div>
        <span class="admin-badge-count"><?php echo $safariCount; ?></span>
      </a>

      <a href="inquiries.php" class="admin-nav-item <?php echo $currentPage == 'inquiries.php' ? 'active' : ''; ?>">
        <div class="admin-nav-item-left">
          <span>💬</span>
          <span>Inquiries</span>
        </div>
        <?php if ($inqCount > 0): ?>
          <span class="admin-badge-count" style="background: #ef4444; color: #fff;"><?php echo $inqCount; ?></span>
        <?php endif; ?>
      </a>

      <a href="settings.php" class="admin-nav-item <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">
        <div class="admin-nav-item-left">
          <span>⚙️</span>
          <span>Resort Settings</span>
        </div>
      </a>
    </nav>

    <!-- Sidebar Footer -->
    <div class="admin-sidebar-footer">
      <a href="../index.php" class="btn-view-site" target="_blank">
        <span>🌐 View Guest Website</span>
      </a>
      <a href="logout.php" class="btn-logout" style="justify-content: center; padding: 0.4rem;">
        <span>🚪 Sign Out</span>
      </a>
    </div>
  </aside>

  <!-- Admin Main Content Area -->
  <div class="admin-main">
    
    <!-- Topbar -->
    <header class="admin-topbar">
      <div style="display: flex; align-items: center; gap: 1rem;">
        <button id="adminMenuToggle" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; display: none;" aria-label="Toggle Menu">
          ☰
        </button>
        <h1 class="admin-topbar-title"><?php echo $pageTitle ?? 'Dashboard Overview'; ?></h1>
      </div>

      <div class="admin-topbar-right">
        <span style="font-size: 0.82rem; color: var(--admin-muted); display: none; sm:inline;">
          📅 <?php echo date('D, d M Y'); ?>
        </span>
        <div class="admin-user-pill">
          <div class="admin-avatar">A</div>
          <span><?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'admin'); ?> (<?php echo htmlspecialchars($_SESSION['admin_role'] ?? 'Super Admin'); ?>)</span>
        </div>
      </div>
    </header>

    <!-- Main Dynamic Content Container -->
    <main class="admin-content">
      
      <?php if (isset($_SESSION['admin_msg'])): ?>
        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 0.8rem 1.2rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
          <span>✓ <?php echo htmlspecialchars($_SESSION['admin_msg']); ?></span>
          <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;">✕</button>
        </div>
        <?php unset($_SESSION['admin_msg']); ?>
      <?php endif; ?>
