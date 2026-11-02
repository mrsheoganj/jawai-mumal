<?php
require __DIR__ . '/_init.php'; require_once __DIR__ . '/_crud.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'update' && $id) {
        $st = in_array($_POST['status'] ?? '', booking_statuses(), true) ? $_POST['status'] : 'New';
        $an = v_clean((string)($_POST['admin_notes'] ?? ''));
        if (mb_strlen($an) > 2000) { flash('err', 'Notes must be 2000 characters or fewer.'); redirect('bookings.php'); }
        q('UPDATE jm_bookings SET status = ?, admin_notes = ? WHERE id = ?', [$st, $an, $id]);
        flash('ok', 'Booking updated.');
    } elseif (($_POST['action'] ?? '') === 'email_confirm' && $id) {
        $b = row('SELECT * FROM jm_bookings WHERE id = ?', [$id]);
        if (!$b || !filter_var((string)$b['email'], FILTER_VALIDATE_EMAIL)) flash('err', 'This guest did not give a valid email address.');
        else {
            $intro = mail_fill(mail_tpl('confirm_intro', "Dear {name},\nWe are pleased to confirm your {type}: {item} — {date}.\nWe look forward to welcoming you. For any change, reply on WhatsApp: {whatsapp}"), $b);
            $r = jm_mail((string)$b['email'], mail_fill(mail_tpl('confirm_subject', 'Your booking {code} is confirmed — {hotel}'), $b), $intro . "\n\n" . implode("\n", array_map(fn($k, $v) => "$k: $v", array_keys(booking_rows($b)), booking_rows($b))), mail_html('Booking confirmed', $intro, booking_rows($b), setting('site_name') . ' · ' . setting('owner1_phone')), 'confirmation');
            if ($r['ok']) { q("UPDATE jm_bookings SET status = 'Confirmed' WHERE id = ? AND status IN ('New','Contacted')", [$id]); flash('ok', 'Confirmation email sent to ' . $b['email'] . ' and the booking is marked Confirmed.'); }
            else flash('err', 'Email failed: ' . $r['error'] . ' (see Admin > Email (SMTP)).');
        }
    } elseif (($_POST['action'] ?? '') === 'delete' && $id) {
        q('DELETE FROM jm_bookings WHERE id = ?', [$id]); flash('ok', 'Booking deleted.');
    }
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
    require_once __DIR__ . '/../includes/export.php';
    $fmt = in_array($_GET['export'], ['xlsx', 'pdf', 'csv'], true) ? $_GET['export'] : 'xlsx';
    $all = rows(str_replace(' LIMIT 300', '', $sql), $par);   // every matching record, not just the 300 on screen
    $types = booking_types();
    $rcv = fn($b) => $b['created_at'] ? date('d M Y H:i', strtotime($b['created_at'])) : '';
    $dates = fn($b) => trim(fmt_date($b['date_from']) . ($b['date_to'] ? ' → ' . fmt_date($b['date_to']) : '') . ($b['time_slot'] ? ' · ' . $b['time_slot'] : ''));
    $filt = array_filter(['type' => $ft ? ($types[$ft] ?? $ft) : '', 'status' => $fs, 'search' => $fq]);
    $sub = 'Bookings & enquiries · ' . ($filt ? 'filter: ' . implode(', ', array_map(fn($k, $v) => "$k = $v", array_keys($filt), $filt)) . ' · ' : 'all records · ') . count($all) . ' record' . (count($all) === 1 ? '' : 's') . ' · generated ' . date('d M Y, H:i');
    if ($fmt === 'csv') {
        $safe = fn($v) => is_string($v) && $v !== '' && strpos("=+-@\t\r", $v[0]) !== false ? "'" . $v : $v;   // stop spreadsheet formulas typed by guests
        header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="' . export_filename('jawai-mumal-bookings', 'csv') . '"');
        $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
        fputcsv($o, ['Ref', 'Received', 'Type', 'Guest', 'Phone', 'Email', 'For', 'From', 'To', 'Time', 'Guests', 'Nights', 'Estimate', 'GST', 'Total', 'Status', 'Guest notes', 'Admin notes'], ',', '"', '\\');
        foreach ($all as $b) fputcsv($o, array_map($safe, [$b['code'], $rcv($b), $types[$b['type']] ?? $b['type'], $b['guest_name'], $b['phone'], $b['email'], $b['item_name'], $b['date_from'], $b['date_to'], $b['time_slot'], $b['guests'], $b['nights'], $b['amount'], $b['tax'], $b['amount'] + $b['tax'], $b['status'], $b['notes'], $b['admin_notes']]), ',', '"', '\\');
        exit;
    }
    if ($fmt === 'xlsx') {
        $cols = [['Ref', 17], ['Received', 17], ['Type', 13], ['Guest', 22], ['Phone', 16], ['Email', 28], ['For', 26], ['From', 13], ['To', 13], ['Time', 12], ['Guests', 8, 'c'], ['Nights', 8, 'c'], ['Estimate', 12, 'm'], ['GST', 10, 'm'], ['Total', 12, 'm'], ['Status', 12, 'c'], ['Guest notes', 38], ['Admin notes', 30]];
        $data = array_map(fn($b) => [$b['code'], $rcv($b), $types[$b['type']] ?? $b['type'], $b['guest_name'], $b['phone'], (string)$b['email'], $b['item_name'], fmt_date($b['date_from']), fmt_date($b['date_to']), (string)$b['time_slot'], (int)$b['guests'], $b['nights'] ?: '', (int)$b['amount'] ?: '', (int)$b['tax'] ?: '', ((int)$b['amount'] + (int)$b['tax']) ?: '', $b['status'], (string)$b['notes'], (string)$b['admin_notes']], $all);
        export_xlsx(export_filename('jawai-mumal-bookings', 'xlsx'), 'Bookings', setting('site_name') . ' – Bookings & enquiries', $sub, $cols, $data);
    }
    $cols = [['Ref', 12], ['Received', 15.5], ['Type', 9], ['Guest', 15], ['Phone', 13], ['For', 17], ['Dates', 20], ['Guests', 6, 'c'], ['Total', 10, 'm'], ['Status', 9, 'c'], ['Guest notes', 26]];
    $data = array_map(fn($b) => [$b['code'], $rcv($b), $types[$b['type']] ?? $b['type'], $b['guest_name'], $b['phone'], $b['item_name'], $dates($b), (int)$b['guests'], ((int)$b['amount'] + (int)$b['tax']) ?: '', $b['status'], (string)$b['notes']], $all);
    export_pdf(export_filename('jawai-mumal-bookings', 'pdf'), 'Bookings & enquiries', $sub, $cols, $data);
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
