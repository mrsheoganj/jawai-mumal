<?php
/**
 * Jawai Mumal - Rooms & Stay Bookings Management
 */
$pageTitle = "Hotel Suites & Stay Bookings";
require_once __DIR__ . '/admin_header.php';

// Handle Action: Update Booking Status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $newStatus = $_GET['status'];
    $bookId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE stay_bookings SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $bookId]);
    $_SESSION['admin_msg'] = "Booking #$bookId updated to '$newStatus'.";
    header("Location: rooms.php");
    exit;
}

// Handle Action: Toggle Room Availability
if (isset($_GET['toggle_room']) && isset($_GET['room_id'])) {
    $roomId = (int)$_GET['room_id'];
    $newRoomStatus = $_GET['toggle_room'] === 'available' ? 'soldout' : 'available';
    $stmt = $pdo->prepare("UPDATE rooms SET status = ? WHERE id = ?");
    $stmt->execute([$newRoomStatus, $roomId]);
    $_SESSION['admin_msg'] = "Suite status changed to '$newRoomStatus'.";
    header("Location: rooms.php");
    exit;
}

// Handle Action: Delete Room
if (isset($_GET['delete_room'])) {
    $roomId = (int)$_GET['delete_room'];
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->execute([$roomId]);
    $_SESSION['admin_msg'] = "Suite removed from inventory.";
    header("Location: rooms.php");
    exit;
}

// Handle Action: Add New Room Suite
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_room') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? 'Luxury Suite');
    $price = (int)($_POST['price'] ?? 30000);
    $size = trim($_POST['size'] ?? '900 sq. ft.');
    $capacity = trim($_POST['capacity'] ?? '2 Adults');
    $view = trim($_POST['view_type'] ?? 'Granite Hill View');
    $image = trim($_POST['image'] ?? 'assets/images/room_tent_suite.jpg');
    $desc = trim($_POST['description'] ?? '');
    $amenities = trim($_POST['amenities'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    $stmt = $pdo->prepare("INSERT INTO rooms (slug, name, type, price, size, capacity, view_type, image, description, amenities, status, inventory, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', 4, 1)");
    $stmt->execute([$slug, $name, $type, $price, $size, $capacity, $view, $image, $desc, $amenities]);

    $_SESSION['admin_msg'] = "New suite '$name' added successfully!";
    header("Location: rooms.php");
    exit;
}

// Fetch all rooms
$rooms = $pdo->query("SELECT * FROM rooms ORDER BY price ASC")->fetchAll();

// Fetch bookings with optional filter
$statusFilter = $_GET['filter'] ?? 'all';
if ($statusFilter !== 'all') {
    $bStmt = $pdo->prepare("SELECT * FROM stay_bookings WHERE status = ? ORDER BY id DESC");
    $bStmt->execute([$statusFilter]);
    $bookings = $bStmt->fetchAll();
} else {
    $bookings = $pdo->query("SELECT * FROM stay_bookings ORDER BY id DESC")->fetchAll();
}
?>

<!-- 1. Room Inventory Section -->
<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <h2 class="admin-card-title">Resort Suite Inventory (<?php echo count($rooms); ?>)</h2>
      <p style="font-size: 0.82rem; color: var(--admin-muted);">Active accommodations visible to guests on website.</p>
    </div>
    <button class="btn-admin btn-admin-gold" onclick="document.getElementById('addRoomModal').style.display='flex'">
      + Add New Suite
    </button>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
    <?php foreach ($rooms as $r): ?>
      <div style="background: var(--admin-sidebar); border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="position: relative; height: 160px;">
          <img src="<?php echo str_starts_with($r['image'], 'assets/') ? '../' . htmlspecialchars($r['image']) : htmlspecialchars($r['image']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
          <span class="status-pill status-<?php echo $r['status']; ?>" style="position: absolute; top: 0.8rem; right: 0.8rem;">
            <?php echo ucfirst($r['status']); ?>
          </span>
        </div>
        <div style="padding: 1.2rem; flex: 1; display: flex; flex-direction: column;">
          <h4 style="font-size: 1.1rem; color: #fff; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($r['name']); ?></h4>
          <span style="font-size: 0.82rem; color: var(--admin-muted); margin-bottom: 0.8rem;">
            <?php echo htmlspecialchars($r['type']); ?> • <?php echo htmlspecialchars($r['size']); ?>
          </span>
          <div style="font-size: 1.2rem; font-weight: 700; color: var(--admin-gold); margin-bottom: 1.2rem;">
            ₹<?php echo number_format($r['price']); ?> <span style="font-size: 0.75rem; color: var(--admin-muted); font-weight: normal;">/ night</span>
          </div>

          <div style="display: flex; gap: 0.6rem; margin-top: auto;">
            <a href="rooms.php?toggle_room=<?php echo $r['status']; ?>&room_id=<?php echo $r['id']; ?>" class="btn-admin btn-admin-outline btn-admin-sm" style="flex: 1; text-align: center;">
              <?php echo $r['status'] === 'available' ? 'Mark Sold Out' : 'Mark Available'; ?>
            </a>
            <a href="rooms.php?delete_room=<?php echo $r['id']; ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Delete this room suite?')">
              Delete
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- 2. Stay Reservations Management Table -->
<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <h2 class="admin-card-title">Suite Reservations Register (<?php echo count($bookings); ?>)</h2>
      <p style="font-size: 0.82rem; color: var(--admin-muted);">Real-time guest bookings, check-in states, and invoices.</p>
    </div>

    <!-- Filter Buttons -->
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <a href="rooms.php?filter=all" class="btn-admin <?php echo $statusFilter == 'all' ? 'btn-admin-gold' : 'btn-admin-outline'; ?> btn-admin-sm">All</a>
      <a href="rooms.php?filter=Confirmed" class="btn-admin <?php echo $statusFilter == 'Confirmed' ? 'btn-admin-gold' : 'btn-admin-outline'; ?> btn-admin-sm">Confirmed</a>
      <a href="rooms.php?filter=Checked In" class="btn-admin <?php echo $statusFilter == 'Checked In' ? 'btn-admin-gold' : 'btn-admin-outline'; ?> btn-admin-sm">Checked In</a>
      <a href="rooms.php?filter=Cancelled" class="btn-admin <?php echo $statusFilter == 'Cancelled' ? 'btn-admin-gold' : 'btn-admin-outline'; ?> btn-admin-sm">Cancelled</a>
    </div>
  </div>

  <div class="admin-table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Booking ID</th>
          <th>Guest Details</th>
          <th>Suite Booked</th>
          <th>Stay Period</th>
          <th>Total Paid</th>
          <th>Special Notes</th>
          <th>Current Status</th>
          <th>Update Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($bookings as $b): ?>
          <tr>
            <td>
              <strong style="color: var(--admin-gold);"><?php echo htmlspecialchars($b['booking_code']); ?></strong><br>
              <small style="color: var(--admin-muted);"><?php echo substr($b['created_at'], 0, 10); ?></small>
            </td>
            <td>
              <div style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($b['guest_name']); ?></div>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($b['phone']); ?></small><br>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($b['email']); ?></small>
            </td>
            <td>
              <strong><?php echo htmlspecialchars($b['room_name']); ?></strong><br>
              <small style="color: var(--admin-muted);"><?php echo $b['guests']; ?> Guests</small>
            </td>
            <td>
              <div><?php echo htmlspecialchars($b['check_in']); ?> → <?php echo htmlspecialchars($b['check_out']); ?></div>
              <small style="color: var(--admin-gold);"><?php echo $b['nights']; ?> Night(s)</small>
            </td>
            <td>
              <strong style="color: #fff; font-size: 1rem;">₹<?php echo number_format($b['total_price']); ?></strong>
            </td>
            <td style="max-width: 200px; font-size: 0.82rem; color: #cbd5e1;">
              <?php echo htmlspecialchars($b['special_requests'] ?: 'None'); ?>
            </td>
            <td>
              <span class="status-pill status-<?php echo strtolower(str_replace(' ', '', $b['status'])); ?>">
                <?php echo htmlspecialchars($b['status']); ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">
                <?php if ($b['status'] !== 'Checked In'): ?>
                  <a href="rooms.php?status=Checked In&id=<?php echo $b['id']; ?>" class="btn-admin btn-admin-outline btn-admin-sm" title="Mark In">Check In</a>
                <?php endif; ?>
                <?php if ($b['status'] === 'Checked In'): ?>
                  <a href="rooms.php?status=Checked Out&id=<?php echo $b['id']; ?>" class="btn-admin btn-admin-outline btn-admin-sm" title="Mark Out">Check Out</a>
                <?php endif; ?>
                <?php if ($b['status'] !== 'Cancelled'): ?>
                  <a href="rooms.php?status=Cancelled&id=<?php echo $b['id']; ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Cancel this stay booking?')">Cancel</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Room Modal -->
<div class="modal-backdrop" id="addRoomModal" style="display: none;">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="document.getElementById('addRoomModal').style.display='none'">✕</button>
    <h3 style="font-size: 1.4rem; color: #fff; margin-bottom: 1.2rem;">Add New Accommodation Suite</h3>
    
    <form action="rooms.php" method="POST">
      <input type="hidden" name="action" value="add_room">

      <div class="admin-form-group">
        <label>Suite Name</label>
        <input type="text" name="name" class="admin-input" required placeholder="e.g. Royal Sunset Kopje Pavilion">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="admin-form-group">
          <label>Category / Type</label>
          <input type="text" name="type" class="admin-input" required placeholder="e.g. Luxury Tent / Villa">
        </div>

        <div class="admin-form-group">
          <label>Price Per Night (₹ INR)</label>
          <input type="number" name="price" class="admin-input" required placeholder="32000">
        </div>

        <div class="admin-form-group">
          <label>Size</label>
          <input type="text" name="size" class="admin-input" placeholder="850 sq. ft.">
        </div>

        <div class="admin-form-group">
          <label>Guest Capacity</label>
          <input type="text" name="capacity" class="admin-input" placeholder="2 Adults + 1 Child">
        </div>
      </div>

      <div class="admin-form-group">
        <label>View Type</label>
        <input type="text" name="view_type" class="admin-input" placeholder="Granite Hill & Boulder View">
      </div>

      <div class="admin-form-group">
        <label>Image URL / Path</label>
        <input type="text" name="image" class="admin-input" value="assets/images/room_tent_suite.jpg">
      </div>

      <div class="admin-form-group">
        <label>Description</label>
        <textarea name="description" class="admin-textarea" rows="3" required placeholder="Describe the luxury suite, furnishings, and view..."></textarea>
      </div>

      <div class="admin-form-group">
        <label>Amenities (Comma separated)</label>
        <input type="text" name="amenities" class="admin-input" placeholder="Private Plunge Pool, Butler On-Call, Stargazing Deck, Starlink Wi-Fi">
      </div>

      <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
        <button type="button" class="btn-admin btn-admin-outline" onclick="document.getElementById('addRoomModal').style.display='none'">Cancel</button>
        <button type="submit" class="btn-admin btn-admin-gold">Save Suite to Catalog</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
