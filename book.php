<?php
require_once __DIR__ . '/includes/db.php';

$errors = [];
$old = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $old = $_POST;
    $tries = array_filter($_SESSION['book_hits'] ?? [], fn($t) => $t > time() - 600);
    if (!csrf_ok()) {
        $errors[] = 'Your session expired. Please try again.';
    } elseif (!empty($_POST['website'])) {
        redirect(url('book.php?done=JM-OK')); // honeypot: pretend success
    } elseif (count($tries) >= 6) {
        $errors[] = 'Too many requests. Please call us instead, or try again in a few minutes.';
    } elseif (!in_array($type, array_keys(booking_types()), true)) {
        $errors[] = 'Unknown request type.';
    } else {
        $name   = trim($_POST['name'] ?? '');
        $phone  = trim($_POST['phone'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $notes  = trim($_POST['notes'] ?? '');
        $guests = max(1, min(500, (int)($_POST['guests'] ?? 1)));
        $from   = trim($_POST['date_from'] ?? '');
        $to     = trim($_POST['date_to'] ?? '');
        $slot   = trim($_POST['time_slot'] ?? '');
        $itemId = (int)($_POST['item_id'] ?? 0);
        $itemName = trim($_POST['item_name'] ?? '');
        $nights = 0; $amount = 0; $tax = 0;
        $isDate = fn($d) => (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;

        if (mb_strlen($name) < 2) $errors[] = 'Please enter your name.';
        if (strlen(preg_replace('/\D/', '', $phone)) < 8) $errors[] = 'Please enter a valid phone number.';
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'That email address does not look right.';
        if ($type !== 'enquiry') {
            if (!$isDate($from) || $from < date('Y-m-d')) $errors[] = 'Please choose a date that is today or later.';
        } elseif ($notes === '') {
            $errors[] = 'Please tell us how we can help.';
        }

        if (!$errors) {
            if ($type === 'stay') {
                if (!$isDate($to) || $to <= $from) $errors[] = 'Check-out must be after check-in.';
                else {
                    $nights = (int)round((strtotime($to) - strtotime($from)) / 86400);
                    if ($nights > 60) $errors[] = 'For stays longer than 60 nights please call us.';
                    $room = $itemId ? row("SELECT * FROM jm_rooms WHERE id=? AND status='active'", [$itemId]) : null;
                    $itemName = $room ? $room['name'] : 'Any available room';
                    if ($room) { $amount = $nights * (int)$room['price']; $tax = (int)round($amount * (float)setting('tax_percent', '12') / 100); }
                }
            } elseif ($type === 'safari') {
                $s = row("SELECT * FROM jm_safaris WHERE id=? AND status='active'", [$itemId]);
                if (!$s) $errors[] = 'Please choose a safari.';
                else {
                    $private = ($_POST['mode'] ?? '') === 'private';
                    $amount = $private ? (int)$s['private_price'] : (int)$s['price_pp'] * $guests;
                    $itemName = $s['title'] . ($private ? ' (private jeep)' : ' (shared, per person)');
                }
            } elseif ($type === 'dine') {
                $itemName = $itemName ?: 'Restaurant';
            } elseif ($type === 'event') {
                $itemName = $itemName ?: 'Event';
            } else {
                $itemName = 'General enquiry';
            }
        }

        if (!$errors) {
            $code = booking_code($type);
            q('INSERT INTO jm_bookings (code,type,guest_name,phone,email,item_id,item_name,date_from,date_to,time_slot,guests,nights,amount,tax,status,notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
              [$code, $type, mb_substr($name, 0, 150), mb_substr($phone, 0, 40), mb_substr($email, 0, 150), $itemId ?: null, mb_substr($itemName, 0, 250),
               $isDate($from) ? $from : null, ($type === 'stay' && $isDate($to)) ? $to : null, mb_substr($slot, 0, 40), $guests, $nights, $amount, $tax, 'New', mb_substr($notes, 0, 2000)]);
            $_SESSION['book_hits'] = array_merge(array_values($tries), [time()]);
            redirect(url('book.php?done=' . urlencode($code)));
        }
        $_GET['type'] = $type; // re-open the same tab
    }
}

$done = isset($_GET['done']) ? preg_replace('/[^A-Z0-9\-]/', '', strtoupper($_GET['done'])) : '';
$b = $done ? row('SELECT * FROM jm_bookings WHERE code = ?', [$done]) : null;

$pageTitle = $done ? 'Request received' : 'Book your stay';
$pageDesc = 'Request a room, table, safari or party booking at ' . setting('site_name') . ', Bera, Jawai.';
$active = '';
$bodyClass = 'nav-solid';
include __DIR__ . '/includes/header.php';
?>
<section class="sec" style="padding-top:9rem">
  <div class="wrap">
    <?php if ($done): ?>
      <div class="done rv">
        <div class="pillar__ic"><?= icon('check', 30) ?></div>
        <h1 style="font-size:2.4rem">Thank you — request received</h1>
        <p>Your reference number is</p>
        <div class="code"><?= e($done) ?></div>
        <?php if ($b): ?>
          <p><strong><?= e($b['item_name']) ?></strong><?php if ($b['date_from']): ?><br><?= e(fmt_date($b['date_from'])) ?><?= $b['date_to'] ? ' → ' . e(fmt_date($b['date_to'])) : '' ?><?= $b['time_slot'] ? ' · ' . e($b['time_slot']) : '' ?><?php endif; ?><br><?= (int)$b['guests'] ?> guest<?= $b['guests'] > 1 ? 's' : '' ?></p>
          <?php if ($b['amount'] > 0): ?><p>Estimated: <strong><?= money($b['amount'] + $b['tax']) ?></strong><?= $b['tax'] ? ' (incl. GST)' : '' ?> — final rate is confirmed by our team.</p><?php endif; ?>
        <?php endif; ?>
        <p>Our team will call or WhatsApp you shortly to confirm. For anything urgent, reach us directly.</p>
        <div class="hero__cta" style="justify-content:center">
          <a class="btn btn--gold" href="<?= e(wa_link('Hello Jawai Mumal, my reference is ' . $done)) ?>" target="_blank" rel="noopener"><?= icon('whatsapp', 20) ?> WhatsApp us</a>
          <a class="btn btn--line" href="<?= e(tel(setting('owner1_phone'))) ?>"><?= icon('phone', 20) ?> Call <?= e(setting('owner1_phone')) ?></a>
        </div>
      </div>
    <?php else: ?>
      <div class="sechead" style="margin-bottom:2rem">
        <span class="eyebrow eyebrow--dark"><?= et('bookpage.eyebrow', 'Reservations') ?></span>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem)"><?= et('bookpage.title', 'Book your stay') ?></h1>
        <p><?= et('bookpage.sub', 'Choose what you need and send a request. We reply personally — no payment is taken online.') ?></p>
      </div>
      <?php foreach ($errors as $er): ?><div class="alert alert--err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
      <?php include __DIR__ . '/includes/booking_panel.php'; ?>
    <?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
