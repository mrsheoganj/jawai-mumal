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
        $errors[] = 'Too many requests. Please message us on WhatsApp instead, or try again in a few minutes.';
    } elseif (!in_array($type, array_keys(booking_types()), true)) {
        $errors[] = 'Unknown request type.';
    } else {
        $name   = v_clean($_POST['name'] ?? '');
        $phone  = v_clean($_POST['phone'] ?? '');
        $email  = v_clean($_POST['email'] ?? '');
        $notes  = v_clean($_POST['notes'] ?? '');
        $guests = (int)($_POST['guests'] ?? 1);
        $from   = trim($_POST['date_from'] ?? '');
        $to     = trim($_POST['date_to'] ?? '');
        $slot   = v_clean($_POST['time_slot'] ?? '');
        $itemId = (int)($_POST['item_id'] ?? 0);
        $itemName = v_clean($_POST['item_name'] ?? '');
        $nights = 0; $amount = 0; $tax = 0;
        $isDate = fn($d) => (bool)preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;
        $maxGuests = ['stay' => 12, 'dine' => 30, 'safari' => 12, 'event' => 500, 'enquiry' => 50][$type] ?? 20;
        $today = date('Y-m-d'); $horizon = date('Y-m-d', strtotime('+2 years'));

        if ($e = v_name($name, 'Your name')) $errors[] = $e;
        [$phoneNorm, $pe] = v_phone($phone); if ($pe) $errors[] = $pe; else $phone = $phoneNorm;
        if ($e = v_email($email)) $errors[] = $e;
        if ($e = v_note($notes, 600, 'Your request')) $errors[] = $e;
        if ($guests < 1 || $guests > $maxGuests) $errors[] = 'Guests must be between 1 and ' . $maxGuests . ' for this request. For larger groups please message us on WhatsApp.';
        if ($slot !== '' && !preg_match('/^[\p{L}\p{N} :.,\-\x{2013}&()\/]{1,40}$/u', $slot)) $errors[] = 'Please choose a valid time.';
        if ($itemName !== '' && mb_strlen($itemName) > 80) $errors[] = 'That selection is not valid.';
        if (($mode = $_POST['mode'] ?? 'shared') !== '' && !in_array($mode, ['shared', 'private'], true)) $errors[] = 'Please choose a jeep type.';
        if ($type !== 'enquiry') {
            if ($e = v_date($from, $type === 'stay' ? 'check-in date' : 'date', $today, $horizon)) $errors[] = $e;
        } elseif ($notes === '') {
            $errors[] = 'Please tell us how we can help.';
        } elseif (mb_strlen($notes) < 5) {
            $errors[] = 'Please write a little more so we can help.';
        }

        if (!$errors) {
            if ($type === 'stay') {
                if ($e = v_date($to, 'check-out date', $today, $horizon)) $errors[] = $e;
                elseif ($to <= $from) $errors[] = 'Check-out must be after check-in.';
                else {
                    $nights = (int)round((strtotime($to) - strtotime($from)) / 86400);
                    if ($nights > 60) $errors[] = 'For stays longer than 60 nights please message us on WhatsApp.';
                    $room = $itemId ? row("SELECT * FROM jm_rooms WHERE id=? AND status='active'", [$itemId]) : null;
                    $itemName = $room ? $room['name'] : 'Any available room';
                    if ($room) { $amount = $nights * (int)$room['price']; $tax = (int)round($amount * gst_rate((int)$room['price']) / 100); }
                }
            } elseif ($type === 'safari') {
                $s = row("SELECT * FROM jm_safaris WHERE id=? AND status='active'", [$itemId]);
                if (!$s) $errors[] = 'Please choose a safari.';
                else {
                    $private = ($_POST['mode'] ?? '') === 'private';
                    $amount = 0;   // safari rates are shared on enquiry
                    $itemName = $s['title'] . ($private ? ' (private jeep)' : ' (shared jeep)');
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
            $saved = row('SELECT * FROM jm_bookings WHERE code = ?', [$code]);
            if ($saved) notify_booking($saved);
            if (($_POST['via'] ?? '') === 'wa' && $saved) redirect(wa_link(booking_wa_message($saved)));   // saved in admin AND opened in WhatsApp
            redirect(url('book.php?done=' . urlencode($code)));
        }
        $_GET['type'] = $type; // re-open the same tab
    }
}

$done = isset($_GET['done']) ? preg_replace('/[^A-Z0-9\-]/', '', strtoupper($_GET['done'])) : '';
$b = $done ? row('SELECT * FROM jm_bookings WHERE code = ?', [$done]) : null;

$seoKey = 'book'; if ($done) $seo = ['noindex' => true];
$active = '';
$bodyClass = 'nav-solid';
include __DIR__ . '/includes/header.php';
?>
<section class="sec" style="padding-top:9rem">
  <div class="wrap">
    <?php if ($done): ?>
      <div class="done rv">
        <div class="pillar__ic"><?= icon('check', 30) ?></div>
        <h1 style="font-size:2.4rem">Thank you, request received</h1>
        <p>Your reference number is</p>
        <div class="code"><?= e($done) ?></div>
        <?php if ($b): ?>
          <p><strong><?= e($b['item_name']) ?></strong><?php if ($b['date_from']): ?><br><?= e(fmt_date($b['date_from'])) ?><?= $b['date_to'] ? ' → ' . e(fmt_date($b['date_to'])) : '' ?><?= $b['time_slot'] ? ' · ' . e($b['time_slot']) : '' ?><?php endif; ?><br><?= (int)$b['guests'] ?> guest<?= $b['guests'] > 1 ? 's' : '' ?></p>
          <?php if ($b['amount'] > 0): ?><p>Estimated: <strong><?= money($b['amount'] + $b['tax']) ?></strong><?= $b['tax'] ? ' (incl. GST)' : '' ?>, final rate is confirmed by our team.</p><?php endif; ?>
        <?php endif; ?>
        <p><?= et('done.text', 'Your request is saved. Our team will message you on WhatsApp shortly to confirm. To speed things up, send the details to us on WhatsApp.') ?></p>
        <div class="hero__cta" style="justify-content:center">
          <a class="wa-ico wa-ico--lg" href="<?= e(wa_link($b ? booking_wa_message($b) : 'Hello Jawai Mumal, my reference is ' . $done)) ?>" target="_blank" rel="noopener" aria-label="<?= e(t('done.wa', 'Send these details on WhatsApp')) ?>" title="<?= e(t('done.wa', 'Send these details on WhatsApp')) ?>"><?= icon('whatsapp', 34) ?></a>
          </div>
      </div>
    <?php else: ?>
      <div class="sechead" style="margin-bottom:2rem">
        <span class="eyebrow eyebrow--dark"><?= et('bookpage.eyebrow', 'Reservations') ?></span>
        <h1 style="font-size:clamp(2.2rem,5vw,3.6rem)"><?= et('bookpage.title', 'Book your stay') ?></h1>
        <p><?= et('bookpage.sub', 'Choose what you need and send a request. We reply personally, no payment is taken online.') ?></p>
      </div>
      <?php foreach ($errors as $er): ?><div class="alert alert--err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
      <?php include __DIR__ . '/includes/booking_panel.php'; ?>
      <?php if ($errors && $old): $keep = array_diff_key($old, array_flip(['_csrf', 'website'])); ?>
      <script>(function(){var d=<?= json_encode($keep, JSON_HEX_TAG | JSON_HEX_AMP) ?>,f=document.querySelector('form[data-type="'+d.type+'"]');if(!f)return;Object.keys(d).forEach(function(k){var e=f.elements[k];if(!e||k==='type')return;if(e.length&&e[0]&&e[0].type==='radio'){Array.prototype.forEach.call(e,function(r){r.checked=r.value===d[k]});}else{e.value=d[k];}});})();</script>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
