<?php
require __DIR__ . '/_init.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset') {
    if (trim($_POST['confirm'] ?? '') !== 'RESET') { flash('err', 'Type RESET in capital letters to confirm.'); redirect('cleanup.php'); }
    $scope = $_POST['scope'] ?? 'all';
    $where = ['all' => '', 'new' => " WHERE status = 'New'", 'cancelled' => " WHERE status = 'Cancelled'", 'enquiry' => " WHERE type = 'enquiry'"][$scope] ?? '';
    $n = (int)val('SELECT COUNT(*) FROM jm_bookings' . $where);
    q('DELETE FROM jm_bookings' . $where);
    if ($where === '') { try { db_type() === 'mysql' ? db()->exec('ALTER TABLE jm_bookings AUTO_INCREMENT = 1') : db()->exec("DELETE FROM sqlite_sequence WHERE name = 'jm_bookings'"); } catch (Throwable $e) {} }
    flash('ok', $n . ' record(s) deleted. Your bookings list is clean.'); redirect('cleanup.php');
}
$total = (int)val('SELECT COUNT(*) FROM jm_bookings');
admin_head('Go-live cleanup', 'cleanup');
?>
<div class="card" style="max-width:900px"><h2>Go-live cleanup</h2>
  <p class="muted" style="margin-top:0">Done testing? Remove the test bookings and enquiries before you launch. Right now there <?= $total === 1 ? 'is 1 record' : 'are ' . $total . ' records' ?>. This cannot be undone, so <a href="bookings.php?export=1">download a CSV backup</a> first if you want one. Only bookings and enquiries are touched: rooms, menu, photos and all other content stay as they are.</p>
  <form method="post" class="grid" style="align-items:end"><?= csrf_field() ?><input type="hidden" name="action" value="reset">
    <div class="c4"><label for="rs">What to delete</label><select id="rs" name="scope"><option value="all">Everything (all bookings &amp; enquiries)</option><option value="new">Only “New” requests</option><option value="enquiry">Only plain enquiries</option><option value="cancelled">Only cancelled</option></select></div>
    <div class="c4"><label for="rc">Type RESET to confirm</label><input id="rc" name="confirm" autocomplete="off" placeholder="RESET"></div>
    <div class="c4"><button class="btn btn--danger"><?= icon('trash', 16) ?> Reset now</button></div></form></div>
<?php admin_foot(); ?>
