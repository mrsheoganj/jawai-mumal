<?php
/**
 * Jawai Mumal - Safari Fleet & Expedition Management
 */
$pageTitle = "Safari Fleet & Slot Dispatch";
require_once __DIR__ . '/admin_header.php';

// Handle Action: Update Safari Status or Fleet Assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'assign_fleet') {
    $bookId = (int)$_POST['booking_id'];
    $gypsy = trim($_POST['assigned_gypsy'] ?? 'Gypsy #1');
    $guide = trim($_POST['assigned_guide'] ?? 'Kumbha Ram Rabari');
    $status = trim($_POST['status'] ?? 'Confirmed');

    $stmt = $pdo->prepare("UPDATE safari_bookings SET assigned_gypsy = ?, assigned_guide = ?, status = ? WHERE id = ?");
    $stmt->execute([$gypsy, $guide, $status, $bookId]);

    $_SESSION['admin_msg'] = "Safari #$bookId assigned to $gypsy with Guide $guide ($status).";
    header("Location: safari.php");
    exit;
}

// Handle Quick Status Action
if (isset($_GET['status']) && isset($_GET['id'])) {
    $newStatus = $_GET['status'];
    $bookId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE safari_bookings SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $bookId]);
    $_SESSION['admin_msg'] = "Safari #$bookId status set to '$newStatus'.";
    header("Location: safari.php");
    exit;
}

// Fetch all safari bookings
$safariBookings = $pdo->query("SELECT * FROM safari_bookings ORDER BY id DESC")->fetchAll();

// Fetch safari packages
$packages = $pdo->query("SELECT * FROM safaris ORDER BY id ASC")->fetchAll();
?>

<!-- 1. Safari Fleet & Active Booking Register -->
<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <h2 class="admin-card-title">Safari Expeditions & Fleet Dispatch (<?php echo count($safariBookings); ?>)</h2>
      <p style="font-size: 0.82rem; color: var(--admin-muted);">Manage 4x4 Gypsies, master Rabari trackers, and guest departure slots.</p>
    </div>
  </div>

  <div class="admin-table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref Code</th>
          <th>Guest Details</th>
          <th>Expedition</th>
          <th>Date & Slot</th>
          <th>Guests & Type</th>
          <th>Total (₹)</th>
          <th>Assigned Fleet & Guide</th>
          <th>Status</th>
          <th>Dispatch / Edit</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($safariBookings as $s): ?>
          <tr>
            <td>
              <strong style="color: var(--admin-gold);"><?php echo htmlspecialchars($s['booking_code']); ?></strong><br>
              <small style="color: var(--admin-muted);"><?php echo substr($s['created_at'], 0, 10); ?></small>
            </td>
            <td>
              <div style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($s['guest_name']); ?></div>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($s['phone']); ?></small>
            </td>
            <td><strong><?php echo htmlspecialchars($s['safari_title']); ?></strong></td>
            <td>
              <div><?php echo htmlspecialchars($s['safari_date']); ?></div>
              <small style="color: var(--admin-gold); font-weight: 600;"><?php echo htmlspecialchars($s['time_slot']); ?></small>
            </td>
            <td>
              <div><?php echo $s['guests']; ?> Guests</div>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($s['vehicle_type']); ?></small>
            </td>
            <td><strong>₹<?php echo number_format($s['total_price']); ?></strong></td>
            <td>
              <div style="font-size: 0.82rem; color: var(--gold-light);">🚙 <?php echo htmlspecialchars($s['assigned_gypsy']); ?></div>
              <div style="font-size: 0.78rem; color: var(--admin-muted);">👤 <?php echo htmlspecialchars($s['assigned_guide']); ?></div>
            </td>
            <td>
              <span class="status-pill status-<?php echo strtolower($s['status']); ?>">
                <?php echo htmlspecialchars($s['status']); ?>
              </span>
            </td>
            <td>
              <button class="btn-admin btn-admin-outline btn-admin-sm" onclick="openDispatchModal(<?php echo htmlspecialchars(json_encode($s)); ?>)">
                Assign Fleet
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 2. Safari Packages Overview -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">Active Safari Itineraries (<?php echo count($packages); ?>)</h2>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
    <?php foreach ($packages as $pkg): ?>
      <div style="background: var(--admin-sidebar); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.5rem; display: flex; flex-direction: column;">
        <span class="pillar-badge" style="position: static; margin-bottom: 0.8rem; display: inline-block; width: fit-content;"><?php echo htmlspecialchars($pkg['badge']); ?></span>
        <h4 style="font-size: 1.15rem; color: #fff; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($pkg['title']); ?></h4>
        <p style="font-size: 0.82rem; color: var(--admin-muted); margin-bottom: 1rem; flex: 1;"><?php echo htmlspecialchars($pkg['description']); ?></p>
        
        <div style="border-top: 1px solid var(--admin-border); padding-top: 0.8rem; font-size: 0.85rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
            <span style="color: var(--admin-muted);">Per Person:</span>
            <strong style="color: var(--admin-gold);">₹<?php echo number_format($pkg['price_per_person']); ?></strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--admin-muted);">Private 4x4 Gypsy:</span>
            <strong style="color: #fff;">₹<?php echo number_format($pkg['private_gypsy_price']); ?></strong>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Dispatch & Assignment Modal -->
<div class="modal-backdrop" id="dispatchModal" style="display: none;">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="document.getElementById('dispatchModal').style.display='none'">✕</button>
    <h3 style="font-size: 1.4rem; color: #fff; margin-bottom: 1.2rem;">Fleet & Tracker Allocation</h3>
    
    <form action="safari.php" method="POST">
      <input type="hidden" name="action" value="assign_fleet">
      <input type="hidden" name="booking_id" id="dispatchBookingId">

      <div class="admin-form-group">
        <label>Guest Booking</label>
        <input type="text" id="dispatchGuestDisplay" class="admin-input" readonly style="opacity: 0.7;">
      </div>

      <div class="admin-form-group">
        <label>Assign 4x4 Gypsy Vehicle</label>
        <select name="assigned_gypsy" id="dispatchGypsySelect" class="admin-select">
          <option value="Gypsy #1 (Royal Olive Green)">Gypsy #1 (Royal Olive Green - Raised Stadium Bench)</option>
          <option value="Gypsy #2 (Desert Sand Khaki)">Gypsy #2 (Desert Sand Khaki - Monopod Mounts)</option>
          <option value="Gypsy #3 (Monolith Granite Grey)">Gypsy #3 (Monolith Granite Grey - Open Canvas)</option>
          <option value="Gypsy #4 (Forest Camo Specialized)">Gypsy #4 (Forest Camo Specialized - Telephoto Ready)</option>
        </select>
      </div>

      <div class="admin-form-group">
        <label>Assign Senior Naturalist / Tracker</label>
        <select name="assigned_guide" id="dispatchGuideSelect" class="admin-select">
          <option value="Kumbha Ram Rabari (Master Tracker)">Kumbha Ram Rabari (Master Tracker, 25 Yrs Experience)</option>
          <option value="Narendra Singh (Senior Wildlife Naturalist)">Narendra Singh (Senior Wildlife Naturalist)</option>
          <option value="Bhanwar Lal (Dam & Wetland Specialist)">Bhanwar Lal (Dam & Wetland Specialist)</option>
          <option value="Devendra Rawat (Astronomy & Night Tracker)">Devendra Rawat (Astronomy & Night Tracker)</option>
        </select>
      </div>

      <div class="admin-form-group">
        <label>Expedition Status</label>
        <select name="status" id="dispatchStatusSelect" class="admin-select">
          <option value="Confirmed">Confirmed</option>
          <option value="In Progress">In Progress (Out on Boulders)</option>
          <option value="Completed">Completed (Returned to Camp)</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

      <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
        <button type="button" class="btn-admin btn-admin-outline" onclick="document.getElementById('dispatchModal').style.display='none'">Cancel</button>
        <button type="submit" class="btn-admin btn-admin-gold">Confirm Fleet Dispatch</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openDispatchModal(booking) {
    document.getElementById('dispatchBookingId').value = booking.id;
    document.getElementById('dispatchGuestDisplay').value = booking.booking_code + ' - ' + booking.guest_name + ' (' + booking.safari_title + ')';
    document.getElementById('dispatchGypsySelect').value = booking.assigned_gypsy;
    document.getElementById('dispatchGuideSelect').value = booking.assigned_guide;
    document.getElementById('dispatchStatusSelect').value = booking.status;
    document.getElementById('dispatchModal').style.display = 'flex';
  }
</script>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
