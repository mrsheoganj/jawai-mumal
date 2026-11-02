<?php
/** Booking & enquiry helpers shared by Admin > Bookings and the /pk-crm page. */

/** Handles the update / email_confirm / delete form posts. Sets flash messages; the caller redirects. */
function crm_post(): void {
    $id = (int)($_POST['id'] ?? 0); $a = $_POST['action'] ?? '';
    if ($a === 'update' && $id) {
        $st = in_array($_POST['status'] ?? '', booking_statuses(), true) ? $_POST['status'] : 'New';
        $an = v_clean((string)($_POST['admin_notes'] ?? ''));
        if (mb_strlen($an) > 2000) { flash('err', 'Notes must be 2000 characters or fewer.'); return; }
        q('UPDATE jm_bookings SET status = ?, admin_notes = ? WHERE id = ?', [$st, $an, $id]);
        flash('ok', 'Booking updated.');
    } elseif ($a === 'status' && $id) {
        $st = in_array($_POST['status'] ?? '', booking_statuses(), true) ? $_POST['status'] : '';
        if ($st !== '') { q('UPDATE jm_bookings SET status = ? WHERE id = ?', [$st, $id]); flash('ok', 'Marked as ' . $st . '.'); }
    } elseif ($a === 'email_confirm' && $id) {
        $b = row('SELECT * FROM jm_bookings WHERE id = ?', [$id]);
        if (!$b || !filter_var((string)$b['email'], FILTER_VALIDATE_EMAIL)) flash('err', 'This guest did not give a valid email address.');
        else {
            $intro = mail_fill(mail_tpl('confirm_intro', "Dear {name},\nWe are pleased to confirm your {type}: {item} — {date}.\nWe look forward to welcoming you. For any change, reply on WhatsApp: {whatsapp}"), $b);
            $r = jm_mail((string)$b['email'], mail_fill(mail_tpl('confirm_subject', 'Your booking {code} is confirmed — {hotel}'), $b), $intro . "\n\n" . implode("\n", array_map(fn($k, $v) => "$k: $v", array_keys(booking_rows($b)), booking_rows($b))), mail_html('Booking confirmed', $intro, booking_rows($b), setting('site_name') . ' · ' . setting('owner1_phone')), 'confirmation');
            if ($r['ok']) { q("UPDATE jm_bookings SET status = 'Confirmed' WHERE id = ? AND status IN ('New','Contacted')", [$id]); flash('ok', 'Confirmation email sent to ' . $b['email'] . ' and the booking is marked Confirmed.'); }
            else flash('err', 'Email failed: ' . $r['error'] . ' (see Admin > Email (SMTP)).');
        }
    } elseif ($a === 'delete' && $id) {
        q('DELETE FROM jm_bookings WHERE id = ?', [$id]); flash('ok', 'Booking deleted.');
    }
}

/** Reads the filter fields from a GET array. */
function crm_filters(array $g): array {
    $t = (string)($g['type'] ?? ''); $s = (string)($g['status'] ?? '');
    $d = fn($k) => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($g[$k] ?? '')) ? (string)$g[$k] : '';
    return ['type' => isset(booking_types()[$t]) ? $t : '', 'status' => in_array($s, booking_statuses(), true) ? $s : '', 'q' => mb_substr(trim((string)($g['q'] ?? '')), 0, 60), 'from' => $d('from'), 'to' => $d('to'),
            'sort' => in_array($g['sort'] ?? '', ['new', 'old', 'arrival'], true) ? $g['sort'] : 'new'];
}
function crm_where(array $f, bool $withStatus = true): array {
    $w = []; $p = [];
    if ($f['type'] !== '') { $w[] = 'type = ?'; $p[] = $f['type']; }
    if ($withStatus && $f['status'] !== '') { $w[] = 'status = ?'; $p[] = $f['status']; }
    if ($f['q'] !== '') { $w[] = '(code LIKE ? OR guest_name LIKE ? OR phone LIKE ? OR email LIKE ? OR item_name LIKE ?)'; array_push($p, ...array_fill(0, 5, '%' . $f['q'] . '%')); }
    if ($f['from'] !== '') { $w[] = 'created_at >= ?'; $p[] = $f['from'] . ' 00:00:00'; }
    if ($f['to'] !== '') { $w[] = 'created_at <= ?'; $p[] = $f['to'] . ' 23:59:59'; }
    return [$w ? ' WHERE ' . implode(' AND ', $w) : '', $p];
}
function crm_order(string $sort): string { return $sort === 'old' ? 'id ASC' : ($sort === 'arrival' ? "(date_from IS NULL), date_from ASC, id DESC" : 'id DESC'); }

/** Sends an Excel / PDF / CSV file of the given bookings and stops. */
function crm_export(string $fmt, array $all, string $sub): void {
    require_once __DIR__ . '/export.php';
    $types = booking_types();
    $rcv = fn($b) => $b['created_at'] ? date('d M Y H:i', strtotime($b['created_at'])) : '';
    $dates = fn($b) => trim(fmt_date($b['date_from']) . ($b['date_to'] ? ' → ' . fmt_date($b['date_to']) : '') . ($b['time_slot'] ? ' · ' . $b['time_slot'] : ''));
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
function crm_subtitle(array $f, int $n): string {
    $types = booking_types();
    $filt = array_filter(['type' => $f['type'] ? ($types[$f['type']] ?? $f['type']) : '', 'status' => $f['status'], 'search' => $f['q'], 'from' => $f['from'], 'to' => $f['to']]);
    return 'Bookings & enquiries · ' . ($filt ? 'filter: ' . implode(', ', array_map(fn($k, $v) => "$k = $v", array_keys($filt), $filt)) . ' · ' : 'all records · ') . $n . ' record' . ($n === 1 ? '' : 's') . ' · generated ' . date('d M Y, H:i');
}
