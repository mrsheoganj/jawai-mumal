<?php
/**
 * Jawai Mumal - Admin Dashboard
 */
$pageTitle = "Operations Dashboard";
require_once __DIR__ . '/admin_header.php';

// Calculate KPI Metrics
$totalStayRevenue = $pdo->query("SELECT SUM(total_price) FROM stay_bookings WHERE status != 'Cancelled'")->fetchColumn() ?: 0;
$totalSafariRevenue = $pdo->query("SELECT SUM(total_price) FROM safari_bookings WHERE status != 'Cancelled'")->fetchColumn() ?: 0;
$totalRevenue = $totalStayRevenue + $totalSafariRevenue;

$activeStaysCount = $pdo->query("SELECT COUNT(*) FROM stay_bookings WHERE status IN ('Confirmed', 'Checked In')")->fetchColumn() ?: 0;
$tableResCount = $pdo->query("SELECT COUNT(*) FROM dining_bookings WHERE status IN ('Confirmed', 'Seated')")->fetchColumn() ?: 0;
$safariExpCount = $pdo->query("SELECT COUNT(*) FROM safari_bookings WHERE status = 'Confirmed'")->fetchColumn() ?: 0;
$pendingInqCount = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'Pending'")->fetchColumn() ?: 0;

// Fetch Recent Stay Bookings
$recentStays = $pdo->query("SELECT * FROM stay_bookings ORDER BY id DESC LIMIT 5")->fetchAll();

// Fetch Recent Dining Bookings
$recentDining = $pdo->query("SELECT * FROM dining_bookings ORDER BY id DESC LIMIT 5")->fetchAll();

// Fetch Recent Safari Bookings
$recentSafaris = $pdo->query("SELECT * FROM safari_bookings ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!-- KPI Metric Cards -->
<div class="metrics-grid">
  <div class="metric-card">
    <div class="metric-header">
      <span class="metric-title">Gross Sanctuary Revenue</span>
      <div class="metric-icon-wrap">₹</div>
    </div>
    <div class="metric-value">₹<?php echo number_format($totalRevenue); ?></div>
    <div class="metric-sub">Stays: ₹<?php echo number_format($totalStayRevenue); ?> | Safaris: ₹<?php echo number_format($totalSafariRevenue); ?></div>
  </div>

  <div class="metric-card">
    <div class="metric-header">
      <span class="metric-title">Active Suite Bookings</span>
      <div class="metric-icon-wrap">🏨</div>
    </div>
    <div class="metric-value"><?php echo $activeStaysCount; ?></div>
    <div class="metric-sub">Confirmed & Checked-In guests</div>
  </div>

  <div class="metric-card">
    <div class="metric-header">
      <span class="metric-title">Safari Expeditions</span>
      <div class="metric-icon-wrap">🐆</div>
    </div>
    <div class="metric-value"><?php echo $safariExpCount; ?></div>
    <div class="metric-sub">4x4 Gypsies deployed</div>
  </div>

  <div class="metric-card">
    <div class="metric-header">
      <span class="metric-title">Dining Covers</span>
      <div class="metric-icon-wrap">🍽️</div>
    </div>
    <div class="metric-value"><?php echo $tableResCount; ?></div>
    <div class="metric-sub">Bush dinners & Rasoi tables</div>
  </div>

  <div class="metric-card">
    <div class="metric-header">
      <span class="metric-title">VIP Inquiries</span>
      <div class="metric-icon-wrap">💬</div>
    </div>
    <div class="metric-value" style="<?php echo $pendingInqCount > 0 ? 'color: #ef4444;' : ''; ?>">
      <?php echo $pendingInqCount; ?>
    </div>
    <div class="metric-sub">Pending concierge follow-ups</div>
  </div>
</div>

<!-- Quick Actions Banner -->
<div style="background: var(--admin-card); border: 1px solid var(--admin-border); border-radius: 12px; padding: 1.2rem 1.6rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h3 style="font-size: 1.05rem; color: #fff; margin-bottom: 0.2rem;">Quick Operations Actions</h3>
    <p style="font-size: 0.82rem; color: var(--admin-muted);">Manage real-time bookings across hotel suites, dining tables, and safari slots.</p>
  </div>
  <div style="display: flex; gap: 0.8rem; flex-wrap: wrap;">
    <a href="rooms.php#addRoom" class="btn-admin btn-admin-gold btn-admin-sm">+ Add New Suite</a>
    <a href="restaurant.php#addDish" class="btn-admin btn-admin-outline btn-admin-sm">+ Add Menu Item</a>
    <a href="safari.php" class="btn-admin btn-admin-outline btn-admin-sm">Assign Gypsy & Tracker</a>
  </div>
</div>

<!-- Recent Stays Table -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">🏨 Recent Suite Bookings</h2>
    <a href="rooms.php" class="btn-admin btn-admin-outline btn-admin-sm">View All Stays →</a>
  </div>

  <div class="admin-table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref Code</th>
          <th>Guest Name</th>
          <th>Suite</th>
          <th>Dates</th>
          <th>Guests</th>
          <th>Total (₹)</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentStays as $stay): ?>
          <tr>
            <td><strong style="color: var(--admin-gold);"><?php echo htmlspecialchars($stay['booking_code']); ?></strong></td>
            <td>
              <div><?php echo htmlspecialchars($stay['guest_name']); ?></div>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($stay['phone']); ?></small>
            </td>
            <td><?php echo htmlspecialchars($stay['room_name']); ?></td>
            <td><?php echo htmlspecialchars($stay['check_in']); ?> → <?php echo htmlspecialchars($stay['check_out']); ?></td>
            <td><?php echo $stay['guests']; ?> Adults</td>
            <td><strong>₹<?php echo number_format($stay['total_price']); ?></strong></td>
            <td>
              <span class="status-pill status-<?php echo strtolower(str_replace(' ', '', $stay['status'])); ?>">
                <?php echo htmlspecialchars($stay['status']); ?>
              </span>
            </td>
            <td>
              <a href="rooms.php?status=Checked In&id=<?php echo $stay['id']; ?>" class="btn-admin btn-admin-outline btn-admin-sm" title="Mark Checked In">Check In</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Two Column Layout: Dining & Safaris -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 2rem;">
  
  <!-- Recent Dining Reservations -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">🍽️ Table Reservations</h2>
      <a href="restaurant.php" class="btn-admin btn-admin-outline btn-admin-sm">Manage →</a>
    </div>

    <div class="admin-table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Guest</th>
            <th>Experience</th>
            <th>Date & Time</th>
            <th>Party</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentDining as $dine): ?>
            <tr>
              <td>
                <div><?php echo htmlspecialchars($dine['guest_name']); ?></div>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($dine['phone']); ?></small>
              </td>
              <td style="font-size: 0.82rem;"><?php echo htmlspecialchars($dine['dining_type']); ?></td>
              <td><?php echo htmlspecialchars($dine['dining_date']); ?><br><small><?php echo htmlspecialchars($dine['time_slot']); ?></small></td>
              <td><?php echo $dine['party_size']; ?></td>
              <td>
                <span class="status-pill status-<?php echo strtolower($dine['status']); ?>">
                  <?php echo htmlspecialchars($dine['status']); ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Safari Expeditions -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">🐆 Safari Expeditions</h2>
      <a href="safari.php" class="btn-admin btn-admin-outline btn-admin-sm">Manage →</a>
    </div>

    <div class="admin-table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Guest</th>
            <th>Expedition</th>
            <th>Date & Slot</th>
            <th>Assigned Vehicle</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentSafaris as $saf): ?>
            <tr>
              <td>
                <div><?php echo htmlspecialchars($saf['guest_name']); ?></div>
                <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($saf['phone']); ?></small>
              </td>
              <td style="font-size: 0.82rem;"><?php echo htmlspecialchars($saf['safari_title']); ?></td>
              <td><?php echo htmlspecialchars($saf['safari_date']); ?><br><small><?php echo htmlspecialchars($saf['time_slot']); ?></small></td>
              <td style="font-size: 0.8rem;"><?php echo htmlspecialchars($saf['assigned_gypsy']); ?></td>
              <td>
                <span class="status-pill status-<?php echo strtolower($saf['status']); ?>">
                  <?php echo htmlspecialchars($saf['status']); ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
