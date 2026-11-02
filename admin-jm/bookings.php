<?php
require __DIR__ . '/_init.php'; require_once __DIR__ . '/_crud.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crm_post();
    redirect('bookings.php?' . http_build_query(array_filter(['type' => $_POST['f_type'] ?? '', 'status' => $_POST['f_status'] ?? ''])));
}
$ft = $_GET['type'] ?? ''; $fs = $_GET['status'] ?? ''; $fq = trim($_GET['q'] ?? '');
$where = []; $par = [];
if ($ft !== '') { $where[] = 'type = ?'; $par[] = $ft; }
if ($fs !== '') { $where[] = 'status = ?'; $par[] = $fs; }
if ($fq !== '') { $where[] = '(code LIKE ? OR guest_name LIKE ? OR phone LIKE ?)'; array_push($par, "%$fq%", "%$fq%", "%$fq%"); }
$sql = 'SELECT * FROM jm_bookings' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC LIMIT 300';
$list = rows($sql, $par);
if (isset($_GET['export'])) {
    $fmt = in_array($_GET['export'], ['xlsx', 'pdf', 'csv'], true) ? $_GET['export'] : 'xlsx';
    $all = rows(str_replace(' LIMIT 300', '', $sql), $par);   // every matching record, not just the 300 on screen
    crm_export($fmt, $all, crm_subtitle(['type' => $ft, 'status' => $fs, 'q' => $fq, 'from' => '', 'to' => ''], count($all)));
}
admin_head('Bookings & enquiries', 'bookings');
?>
<div class="toolbar">
  <form method="get"><select name="type" aria-label="Type"><option value="">All types</option><?php foreach (booking_types() as $k => $v): ?><option value="<?= $k ?>" <?= $ft === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select>
    <select name="status" aria-label="Status"><option value="">All statuses</option><?php foreach (booking_statuses() as $s): ?><option <?= $fs === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select>
    <input name="q" value="<?= e($fq) ?>" placeholder="Search name, phone or ref" style="width:220px"><button class="btn btn--line btn--sm"><?= icon('search', 16) ?> Filter</button></form>
  <a class="btn btn--line btn--sm" href="?<?= e(http_build_query(array_filter(['type' => $ft, 'status' => $fs, 'q' => $fq, 'export' => 'xlsx']))) ?>"><?= icon('download', 16) ?> Excel</a>
  <a class="btn btn--line btn--sm" href="?<?= e(http_build_query(array_filter(['type' => $ft, 'status' => $fs, 'q' => $fq, 'export' => 'pdf']))) ?>"><?= icon('download', 16) ?> PDF</a>
  <a class="btn btn--line btn--sm" href="?<?= e(http_build_query(array_filter(['type' => $ft, 'status' => $fs, 'q' => $fq, 'export' => 'csv']))) ?>"><?= icon('download', 16) ?> CSV</a>
</div>
<div class="tablewrap"><table>
  <thead><tr><th>Ref / received</th><th>Guest</th><th>Request</th><th>Dates</th><th>Status</th><th></th></tr></thead><tbody>
  <?php foreach ($list as $b): ?>
    <tr>
      <td><strong><?= e($b['code']) ?></strong><br><span class="muted"><?= e(date('d M, g:i a', strtotime($b['created_at']))) ?></span></td>
      <td><strong><?= e($b['guest_name']) ?></strong><br><?= e($b['phone']) ?> <a class="wa-ico wa-ico--sm" href="<?= e(wa_to($b['phone'], 'Hello ' . $b['guest_name'] . ', this is ' . setting('site_name') . ' about your request ' . $b['code'] . '.')) ?>" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp"><?= icon('whatsapp', 18) ?></a><?= $b['email'] ? '<br><span class="muted">' . e($b['email']) . '</span>' : '' ?></td>
      <td><span class="pill"><?= e(booking_types()[$b['type']] ?? $b['type']) ?></span><br><?= e($b['item_name']) ?><br><span class="muted"><?= (int)$b['guests'] ?> guest<?= $b['guests'] > 1 ? 's' : '' ?><?= $b['amount'] ? ' · est. ' . money($b['amount'] + $b['tax']) : '' ?></span></td>
      <td><?= $b['date_from'] ? e(fmt_date($b['date_from'])) : '—' ?><?= $b['date_to'] ? '<br>→ ' . e(fmt_date($b['date_to'])) . ' (' . (int)$b['nights'] . 'n)' : '' ?><?= $b['time_slot'] ? '<br>' . e($b['time_slot']) : '' ?></td>
      <td><?= pill_status($b['status']) ?></td>
      <td><details class="bk"><summary class="btn btn--line btn--sm"><?= icon('pencil', 16) ?> Manage</summary>
        <form method="post" style="min-width:260px;margin-top:.8rem;display:grid;gap:.6rem"><?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><input type="hidden" name="f_type" value="<?= e($ft) ?>"><input type="hidden" name="f_status" value="<?= e($fs) ?>">
          <?php if ($b['notes']): ?><p class="help"><strong>Guest note:</strong> <?= e($b['notes']) ?></p><?php endif; ?>
          <select name="status" aria-label="Status"><?php foreach (booking_statuses() as $s): ?><option <?= $b['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select>
          <textarea name="admin_notes" rows="2" maxlength="2000" placeholder="Private notes (not shown to guest)"><?= e($b['admin_notes']) ?></textarea>
          <button class="btn btn--gold btn--sm">Save</button>
          <a class="wa-ico" target="_blank" rel="noopener" href="<?= e(wa_to($b['phone'], booking_wa_message($b, true) . "\n\nYour request is confirmed. We look forward to welcoming you. For any change, reply here or call " . setting('owner1_phone') . '.')) ?>" aria-label="Send confirmation on WhatsApp" title="Send confirmation on WhatsApp"><?= icon('whatsapp', 22) ?></a></form>
        <?php if (filter_var((string)$b['email'], FILTER_VALIDATE_EMAIL)): ?><form method="post" style="margin-top:.5rem"><?= csrf_field() ?><input type="hidden" name="action" value="email_confirm"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn btn--line btn--sm" data-confirm="Email a confirmation to <?= e($b['email']) ?> and mark this booking Confirmed?"><?= icon('mail', 16) ?> Email confirmation</button></form><?php endif; ?>
        <form method="post" style="margin-top:.5rem"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$b['id'] ?>"><button class="btn btn--danger btn--sm" data-confirm="Delete this booking permanently?"><?= icon('trash', 16) ?> Delete</button></form></details></td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$list): ?><tr><td colspan="6" class="muted" style="text-align:center;padding:2rem">No bookings match.</td></tr><?php endif; ?>
  </tbody></table></div>
<?php admin_foot(); ?>
