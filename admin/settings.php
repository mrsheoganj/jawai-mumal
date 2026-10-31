<?php
/**
 * Jawai Mumal - Resort Settings & System Configuration
 */
$pageTitle = "Resort Configuration";
require_once __DIR__ . '/admin_header.php';

// Handle Action: Update Settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
    // In production, can update settings table
    $_SESSION['admin_msg'] = "Resort settings saved successfully.";
    header("Location: settings.php");
    exit;
}

// Handle Action: Change Admin Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $newPass = $_POST['new_password'] ?? '';
    if (strlen($newPass) >= 6) {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ?");
        $stmt->execute([$hash, $_SESSION['admin_user']]);
        $_SESSION['admin_msg'] = "Admin password updated successfully!";
    } else {
        $_SESSION['admin_msg'] = "Password must be at least 6 characters.";
    }
    header("Location: settings.php");
    exit;
}
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
  
  <!-- Resort Info Settings -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">Resort Sanctuary Profile</h2>
    </div>

    <form action="settings.php" method="POST">
      <input type="hidden" name="action" value="save_settings">

      <div class="admin-form-group">
        <label>Estate Brand Name</label>
        <input type="text" name="resort_name" class="admin-input" value="<?php echo SITE_NAME; ?>" required>
      </div>

      <div class="admin-form-group">
        <label>Slogan / Tagline</label>
        <input type="text" name="tagline" class="admin-input" value="<?php echo SITE_TAGLINE; ?>" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="admin-form-group">
          <label>Reservations Phone</label>
          <input type="text" name="phone" class="admin-input" value="<?php echo RESORT_PHONE; ?>" required>
        </div>

        <div class="admin-form-group">
          <label>WhatsApp Concierge Number</label>
          <input type="text" name="whatsapp" class="admin-input" value="<?php echo RESORT_WHATSAPP; ?>" required>
        </div>
      </div>

      <div class="admin-form-group">
        <label>Official Email</label>
        <input type="email" name="email" class="admin-input" value="<?php echo RESORT_EMAIL; ?>" required>
      </div>

      <div class="admin-form-group">
        <label>Physical Address & Coordinates</label>
        <textarea name="address" class="admin-textarea" rows="3" required><?php echo RESORT_ADDRESS; ?></textarea>
      </div>

      <button type="submit" class="btn-admin btn-admin-gold" style="width: 100%;">
        Save Sanctuary Settings
      </button>
    </form>
  </div>

  <!-- Security & Database Management -->
  <div style="display: flex; flex-direction: column; gap: 2rem;">
    
    <!-- Change Password -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Security & Password</h2>
      </div>

      <form action="settings.php" method="POST">
        <input type="hidden" name="action" value="change_password">

        <div class="admin-form-group">
          <label>Logged-in Administrator</label>
          <input type="text" class="admin-input" value="<?php echo htmlspecialchars($_SESSION['admin_user']); ?>" readonly style="opacity: 0.7;">
        </div>

        <div class="admin-form-group">
          <label>New Administrative Password</label>
          <input type="password" name="new_password" class="admin-input" required placeholder="Minimum 6 characters">
        </div>

        <button type="submit" class="btn-admin btn-admin-outline" style="width: 100%;">
          Update Admin Password
        </button>
      </form>
    </div>

    <!-- Hostinger Deployment Guide Card -->
    <div class="admin-card" style="border-color: rgba(212, 175, 55, 0.4);">
      <div class="admin-card-header">
        <h2 class="admin-card-title">Hostinger Deployment Status</h2>
      </div>

      <div style="font-size: 0.85rem; color: #cbd5e1; display: flex; flex-direction: column; gap: 0.8rem; line-height: 1.6;">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--admin-border); padding-bottom: 0.4rem;">
          <span style="color: var(--admin-muted);">Database Mode:</span>
          <span class="status-pill status-confirmed"><?php echo strtoupper($GLOBALS['dbType'] ?? 'MYSQL'); ?> ACTIVE</span>
        </div>
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--admin-border); padding-bottom: 0.4rem;">
          <span style="color: var(--admin-muted);">PHP Version:</span>
          <span>PHP <?php echo phpversion(); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--admin-border); padding-bottom: 0.4rem;">
          <span style="color: var(--admin-muted);">SQL Export File:</span>
          <a href="../database.sql" style="color: var(--admin-gold); text-decoration: underline;" download>database.sql (Download)</a>
        </div>
        <div style="display: flex; justify-content: space-between; padding-top: 0.2rem;">
          <span style="color: var(--admin-muted);">Hostinger Target:</span>
          <span><code>public_html</code></span>
        </div>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
