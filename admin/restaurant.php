<?php
/**
 * Jawai Mumal - Restaurant & Table Reservations Management
 */
$pageTitle = "Restaurant & Menu Operations";
require_once __DIR__ . '/admin_header.php';

// Handle Action: Update Dining Booking Status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $newStatus = $_GET['status'];
    $bookId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE dining_bookings SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $bookId]);
    $_SESSION['admin_msg'] = "Table reservation #$bookId updated to '$newStatus'.";
    header("Location: restaurant.php");
    exit;
}

// Handle Action: Toggle Dish Availability (86'd)
if (isset($_GET['toggle_dish']) && isset($_GET['dish_id'])) {
    $dishId = (int)$_GET['dish_id'];
    $newAvail = (int)$_GET['toggle_dish'] === 1 ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE menu_items SET available = ? WHERE id = ?");
    $stmt->execute([$newAvail, $dishId]);
    $_SESSION['admin_msg'] = "Dish availability updated.";
    header("Location: restaurant.php");
    exit;
}

// Handle Action: Delete Dish
if (isset($_GET['delete_dish'])) {
    $dishId = (int)$_GET['delete_dish'];
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$dishId]);
    $_SESSION['admin_msg'] = "Dish removed from menu.";
    header("Location: restaurant.php");
    exit;
}

// Handle Action: Add New Dish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_dish') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Royal Mewari');
    $price = (int)($_POST['price'] ?? 850);
    $isVeg = isset($_POST['is_veg']) ? 1 : 0;
    $spice = trim($_POST['spice_level'] ?? 'Medium');
    $desc = trim($_POST['description'] ?? '');
    $tag = trim($_POST['tag'] ?? 'Chef Special');

    $stmt = $pdo->prepare("INSERT INTO menu_items (name, category, price, is_veg, spice_level, description, tag, available) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([$name, $category, $price, $isVeg, $spice, $desc, $tag]);

    $_SESSION['admin_msg'] = "New dish '$name' added to menu!";
    header("Location: restaurant.php");
    exit;
}

// Fetch all menu items
$menuItems = $pdo->query("SELECT * FROM menu_items ORDER BY category ASC, id ASC")->fetchAll();

// Fetch dining reservations
$diningBookings = $pdo->query("SELECT * FROM dining_bookings ORDER BY id DESC")->fetchAll();
?>

<!-- 1. Table Bookings Section -->
<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <h2 class="admin-card-title">Dining & Bush Dinner Reservations (<?php echo count($diningBookings); ?>)</h2>
      <p style="font-size: 0.82rem; color: var(--admin-muted);">Incoming table bookings for the Mewari Rasoi and Bush Dinners.</p>
    </div>
  </div>

  <div class="admin-table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Ref Code</th>
          <th>Guest Details</th>
          <th>Dining Experience</th>
          <th>Date & Time</th>
          <th>Party Size</th>
          <th>Table Preference</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($diningBookings as $d): ?>
          <tr>
            <td><strong style="color: var(--admin-gold);"><?php echo htmlspecialchars($d['booking_code']); ?></strong></td>
            <td>
              <div style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($d['guest_name']); ?></div>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($d['phone']); ?></small>
            </td>
            <td><strong><?php echo htmlspecialchars($d['dining_type']); ?></strong></td>
            <td>
              <div><?php echo htmlspecialchars($d['dining_date']); ?></div>
              <small style="color: var(--admin-gold);"><?php echo htmlspecialchars($d['time_slot']); ?></small>
            </td>
            <td><?php echo $d['party_size']; ?> Guests</td>
            <td style="font-size: 0.85rem; color: #cbd5e1;"><?php echo htmlspecialchars($d['table_preference'] ?: 'Any'); ?></td>
            <td>
              <span class="status-pill status-<?php echo strtolower($d['status']); ?>">
                <?php echo htmlspecialchars($d['status']); ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 0.3rem;">
                <?php if ($d['status'] !== 'Seated' && $d['status'] !== 'Completed'): ?>
                  <a href="restaurant.php?status=Seated&id=<?php echo $d['id']; ?>" class="btn-admin btn-admin-outline btn-admin-sm">Seat</a>
                <?php endif; ?>
                <?php if ($d['status'] === 'Seated'): ?>
                  <a href="restaurant.php?status=Completed&id=<?php echo $d['id']; ?>" class="btn-admin btn-admin-gold btn-admin-sm">Complete</a>
                <?php endif; ?>
                <?php if ($d['status'] !== 'Cancelled'): ?>
                  <a href="restaurant.php?status=Cancelled&id=<?php echo $d['id']; ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Cancel table booking?')">Cancel</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 2. Menu Items Management -->
<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <h2 class="admin-card-title">Live Menu & Culinary Catalog (<?php echo count($menuItems); ?>)</h2>
      <p style="font-size: 0.82rem; color: var(--admin-muted);">Manage dishes, prices, categories, and real-time 86'd out-of-stock toggles.</p>
    </div>
    <button class="btn-admin btn-admin-gold" onclick="document.getElementById('addDishModal').style.display='flex'">
      + Add New Menu Item
    </button>
  </div>

  <div class="admin-table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Dish Name</th>
          <th>Category</th>
          <th>Diet</th>
          <th>Price (₹)</th>
          <th>Spice Level</th>
          <th>Tag</th>
          <th>Stock State</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($menuItems as $m): ?>
          <tr>
            <td>
              <strong style="color: #fff;"><?php echo htmlspecialchars($m['name']); ?></strong><br>
              <small style="color: var(--admin-muted); font-size: 0.78rem;"><?php echo htmlspecialchars($m['description']); ?></small>
            </td>
            <td><span class="dish-tag"><?php echo htmlspecialchars($m['category']); ?></span></td>
            <td>
              <?php if ($m['is_veg']): ?>
                <span style="color: #22c55e; font-weight: 600;">● Veg</span>
              <?php else: ?>
                <span style="color: #ef4444; font-weight: 600;">▲ Non-Veg</span>
              <?php endif; ?>
            </td>
            <td><strong>₹<?php echo number_format($m['price']); ?></strong></td>
            <td><?php echo htmlspecialchars($m['spice_level']); ?></td>
            <td><small><?php echo htmlspecialchars($m['tag']); ?></small></td>
            <td>
              <span class="status-pill status-<?php echo $m['available'] ? 'available' : 'soldout'; ?>">
                <?php echo $m['available'] ? 'In Stock' : '86\'d (Out)'; ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 0.4rem;">
                <a href="restaurant.php?toggle_dish=<?php echo $m['available']; ?>&dish_id=<?php echo $m['id']; ?>" class="btn-admin btn-admin-outline btn-admin-sm">
                  <?php echo $m['available'] ? '86 Dish' : 'Restore'; ?>
                </a>
                <a href="restaurant.php?delete_dish=<?php echo $m['id']; ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Delete this dish?')">
                  ✕
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Dish Modal -->
<div class="modal-backdrop" id="addDishModal" style="display: none;">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="document.getElementById('addDishModal').style.display='none'">✕</button>
    <h3 style="font-size: 1.4rem; color: #fff; margin-bottom: 1.2rem;">Add New Culinary Dish</h3>
    
    <form action="restaurant.php" method="POST">
      <input type="hidden" name="action" value="add_dish">

      <div class="admin-form-group">
        <label>Dish Name</label>
        <input type="text" name="name" class="admin-input" required placeholder="e.g. Royal Mewari Safed Maas">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="admin-form-group">
          <label>Category</label>
          <select name="category" class="admin-select" required>
            <option value="Royal Mewari">Royal Mewari</option>
            <option value="Bush & Bonfire Grill">Bush & Bonfire Grill</option>
            <option value="All-Day Gourmet">All-Day Gourmet</option>
            <option value="Signature Desserts">Signature Desserts</option>
            <option value="Artisanal Drinks">Artisanal Drinks</option>
          </select>
        </div>

        <div class="admin-form-group">
          <label>Price (₹ INR)</label>
          <input type="number" name="price" class="admin-input" required placeholder="1250">
        </div>

        <div class="admin-form-group">
          <label>Spice Level</label>
          <select name="spice_level" class="admin-select">
            <option value="Mild">Mild</option>
            <option value="Medium" selected>Medium</option>
            <option value="High">High</option>
            <option value="Sweet">Sweet</option>
            <option value="Refreshing">Refreshing</option>
          </select>
        </div>

        <div class="admin-form-group">
          <label>Special Tag</label>
          <input type="text" name="tag" class="admin-input" placeholder="e.g. Chef Signature / Bestseller">
        </div>
      </div>

      <div class="admin-form-group" style="flex-direction: row; align-items: center; gap: 0.6rem;">
        <input type="checkbox" name="is_veg" id="isVegCheck" value="1" checked style="width: 18px; height: 18px;">
        <label for="isVegCheck" style="margin-bottom: 0; cursor: pointer;">Vegetarian Dish</label>
      </div>

      <div class="admin-form-group">
        <label>Dish Description & Heirloom Ingredients</label>
        <textarea name="description" class="admin-textarea" rows="3" required placeholder="Describe slow-cooking method, spices, and accompaniments..."></textarea>
      </div>

      <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
        <button type="button" class="btn-admin btn-admin-outline" onclick="document.getElementById('addDishModal').style.display='none'">Cancel</button>
        <button type="submit" class="btn-admin btn-admin-gold">Add to Menu</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
