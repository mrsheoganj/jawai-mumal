<?php
/**
 * Jawai Mumal - Guest Inquiries & VIP Concierge
 */
$pageTitle = "VIP Inquiries & Guest Messages";
require_once __DIR__ . '/admin_header.php';

// Handle Action: Update Inquiry Status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $newStatus = $_GET['status'];
    $inqId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $inqId]);
    $_SESSION['admin_msg'] = "Inquiry #$inqId updated to '$newStatus'.";
    header("Location: inquiries.php");
    exit;
}

// Handle Action: Delete Inquiry
if (isset($_GET['delete'])) {
    $inqId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
    $stmt->execute([$inqId]);
    $_SESSION['admin_msg'] = "Inquiry #$inqId deleted.";
    header("Location: inquiries.php");
    exit;
}

// Fetch all inquiries
$inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY id DESC")->fetchAll();
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <h2 class="admin-card-title">Guest Inquiries & Buyout Requests (<?php echo count($inquiries); ?>)</h2>
      <p style="font-size: 0.82rem; color: var(--admin-muted);">Direct requests submitted via the sanctuary contact form.</p>
    </div>
  </div>

  <div class="admin-table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Sender</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($inquiries as $inq): ?>
          <tr>
            <td style="white-space: nowrap; font-size: 0.82rem; color: var(--admin-muted);">
              <?php echo substr($inq['created_at'], 0, 10); ?>
            </td>
            <td>
              <strong style="color: #fff;"><?php echo htmlspecialchars($inq['name']); ?></strong><br>
              <small><a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" style="color: var(--admin-gold);"><?php echo htmlspecialchars($inq['email']); ?></a></small><br>
              <small style="color: var(--admin-muted);"><?php echo htmlspecialchars($inq['phone'] ?: 'No phone'); ?></small>
            </td>
            <td style="font-weight: 600; color: #fff;">
              <?php echo htmlspecialchars($inq['subject']); ?>
            </td>
            <td style="max-width: 320px; font-size: 0.85rem; color: #cbd5e1; line-height: 1.5;">
              <?php echo nl2br(htmlspecialchars($inq['message'])); ?>
            </td>
            <td>
              <span class="status-pill status-<?php echo strtolower($inq['status']); ?>">
                <?php echo htmlspecialchars($inq['status']); ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                <?php if ($inq['status'] === 'Pending'): ?>
                  <a href="inquiries.php?status=Contacted&id=<?php echo $inq['id']; ?>" class="btn-admin btn-admin-outline btn-admin-sm">Mark Contacted</a>
                <?php endif; ?>
                <?php if ($inq['status'] !== 'Resolved'): ?>
                  <a href="inquiries.php?status=Resolved&id=<?php echo $inq['id']; ?>" class="btn-admin btn-admin-gold btn-admin-sm">Resolve</a>
                <?php endif; ?>
                <a href="inquiries.php?delete=<?php echo $inq['id']; ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Delete this inquiry message?')">✕</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
