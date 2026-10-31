<?php
/** Booking / enquiry panel used on home and book.php. Expects optional $bookTab, $bookRoom, $bookSafari. */
$bookTab = $_GET['type'] ?? ($bookTab ?? 'stay');
if (!in_array($bookTab, ['stay', 'dine', 'safari', 'event', 'enquiry'], true)) $bookTab = 'stay';
$bRooms = rows("SELECT id,name,price FROM jm_rooms WHERE status='active' ORDER BY sort_order,id");
$bSafaris = rows("SELECT id,title,price_pp,private_price FROM jm_safaris WHERE status='active' ORDER BY sort_order,id");
$slots = array_values(array_filter(array_map('trim', explode(',', setting('dine_slots')))));
$maxG = max(1, (int)setting('max_guests', '12'));
$sel = fn($a, $b) => (string)$a === (string)$b ? ' selected' : '';
$preRoom = (int)($_GET['room'] ?? ($bookRoom ?? 0));
$preSaf  = (int)($_GET['safari'] ?? ($bookSafari ?? 0));
$tabs = ['stay' => ['bed', t('book.tab.stay', 'Stay')], 'dine' => ['utensils', t('book.tab.dine', 'Table')], 'safari' => ['car', t('book.tab.safari', 'Safari')], 'event' => ['party', t('book.tab.event', 'Party & Events')], 'enquiry' => ['message', t('book.tab.enquiry', 'Enquiry')]];
$guestOpts = function ($def = 2) use ($maxG) { $o = ''; for ($i = 1; $i <= $maxG; $i++) $o .= '<option value="' . $i . '"' . ($i === $def ? ' selected' : '') . '>' . $i . ' ' . ($i === 1 ? 'guest' : 'guests') . '</option>'; return $o; };
$formHead = function ($type) { return '<form method="post" action="' . e(url('book.php')) . '" class="bform" data-type="' . $type . '" novalidate>' . csrf_field() . '<input type="hidden" name="type" value="' . $type . '"><div class="hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>'; };
?>
<div class="bookbox" id="bookbox">
  <div class="tabs" role="tablist" aria-label="Booking type">
    <?php foreach ($tabs as $k => $t): ?>
      <button type="button" class="tab" role="tab" id="tab-<?= $k ?>" data-tab="<?= $k ?>" aria-controls="panel-<?= $k ?>" aria-selected="<?= $bookTab === $k ? 'true' : 'false' ?>"><?= icon($t[0], 20) ?><?= e($t[1]) ?></button>
    <?php endforeach; ?>
  </div>

  <!-- STAY -->
  <section class="panel" id="panel-stay" role="tabpanel" aria-labelledby="tab-stay" <?= $bookTab === 'stay' ? '' : 'hidden' ?>>
    <?= $formHead('stay') ?>
    <div class="fgrid">
      <div class="f"><label for="s-in">Check-in</label><input type="date" id="s-in" name="date_from" required></div>
      <div class="f"><label for="s-out">Check-out</label><input type="date" id="s-out" name="date_to" required></div>
      <div class="f"><label for="s-g">Guests</label><select id="s-g" name="guests"><?= $guestOpts(2) ?></select></div>
      <div class="f"><label for="s-room">Room</label>
        <select id="s-room" name="item_id"><option value="0" data-price="0">Any available room</option>
        <?php foreach ($bRooms as $r): ?><option value="<?= $r['id'] ?>" data-price="<?= (int)$r['price'] ?>"<?= $sel($preRoom, $r['id']) ?>><?= e($r['name']) ?> — <?= money($r['price']) ?>/night</option><?php endforeach; ?></select></div>
      <div class="f f--4"><label for="s-n">Your name</label><input id="s-n" name="name" autocomplete="name" required></div>
      <div class="f f--4"><label for="s-p">Phone / WhatsApp</label><input id="s-p" name="phone" type="tel" inputmode="tel" autocomplete="tel" required></div>
      <div class="f f--4"><label for="s-e">Email (optional)</label><input id="s-e" name="email" type="email" autocomplete="email"></div>
      <div class="f f--12"><label for="s-no">Requests (optional)</label><input id="s-no" name="notes" placeholder="Arrival time, extra bed, celebration…"></div>
    </div>
    <div class="formfoot"><p><?= icon('shield-check', 18) ?><span><?= et('book.stay.note', 'No payment online. Our team confirms availability and the final rate by call or WhatsApp.') ?></span></p>
      <div><span class="est" data-est></span> <button class="btn btn--gold" type="submit"><?= et('book.stay.btn', 'Request booking') ?> <?= icon('arrow-right', 18) ?></button></div></div>
    </form>
  </section>

  <!-- DINE -->
  <section class="panel" id="panel-dine" role="tabpanel" aria-labelledby="tab-dine" <?= $bookTab === 'dine' ? '' : 'hidden' ?>>
    <?= $formHead('dine') ?>
    <div class="fgrid">
      <div class="f"><label for="d-d">Date</label><input type="date" id="d-d" name="date_from" required></div>
      <div class="f"><label for="d-t">Time</label><select id="d-t" name="time_slot"><?php foreach ($slots as $s): ?><option><?= e($s) ?></option><?php endforeach; ?></select></div>
      <div class="f"><label for="d-g">Guests</label><select id="d-g" name="guests"><?= $guestOpts(2) ?></select></div>
      <div class="f"><label for="d-w">Seating</label><select id="d-w" name="item_name"><option>Restaurant</option><option>Café</option><option>Outdoor / terrace</option></select></div>
      <div class="f f--4"><label for="d-n">Your name</label><input id="d-n" name="name" autocomplete="name" required></div>
      <div class="f f--4"><label for="d-p">Phone / WhatsApp</label><input id="d-p" name="phone" type="tel" inputmode="tel" autocomplete="tel" required></div>
      <div class="f f--4"><label for="d-no">Requests (optional)</label><input id="d-no" name="notes" placeholder="Jain / vegetarian, birthday cake…"></div>
    </div>
    <div class="formfoot"><p><?= icon('clock', 18) ?><span><?= et('book.dine.note', 'Table requests are confirmed by call or WhatsApp. Our kitchen is 100% vegetarian; Jain options on request.') ?></span></p>
      <button class="btn btn--gold" type="submit">Request a table <?= icon('arrow-right', 18) ?></button></div>
    </form>
  </section>

  <!-- SAFARI -->
  <section class="panel" id="panel-safari" role="tabpanel" aria-labelledby="tab-safari" <?= $bookTab === 'safari' ? '' : 'hidden' ?>>
    <?= $formHead('safari') ?>
    <div class="fgrid">
      <div class="f f--6"><label for="f-s">Safari</label>
        <select id="f-s" name="item_id" required><?php foreach ($bSafaris as $s): ?><option value="<?= $s['id'] ?>" data-pp="<?= (int)$s['price_pp'] ?>" data-pv="<?= (int)$s['private_price'] ?>"<?= $sel($preSaf, $s['id']) ?>><?= e($s['title']) ?></option><?php endforeach; ?></select></div>
      <div class="f"><label for="f-d">Date</label><input type="date" id="f-d" name="date_from" required></div>
      <div class="f"><label for="f-g">Guests</label><select id="f-g" name="guests"><?= $guestOpts(2) ?></select></div>
      <div class="f f--12"><span class="lbl">Jeep type</span><div class="seg"><label><input type="radio" name="mode" value="shared" checked> Per person (shared jeep)</label><label><input type="radio" name="mode" value="private"> Private jeep</label></div></div>
      <div class="f f--4"><label for="f-n">Your name</label><input id="f-n" name="name" autocomplete="name" required></div>
      <div class="f f--4"><label for="f-p">Phone / WhatsApp</label><input id="f-p" name="phone" type="tel" inputmode="tel" autocomplete="tel" required></div>
      <div class="f f--4"><label for="f-no">Requests (optional)</label><input id="f-no" name="notes" placeholder="Photography, pick-up point…"></div>
    </div>
    <div class="formfoot"><p><?= icon('compass', 18) ?><span><?= et('book.safari.note', 'Safari timings depend on weather and tracker availability. We confirm the exact slot when we call.') ?></span></p>
      <div><span class="est" data-est></span> <button class="btn btn--gold" type="submit">Request safari <?= icon('arrow-right', 18) ?></button></div></div>
    </form>
  </section>

  <!-- EVENT -->
  <section class="panel" id="panel-event" role="tabpanel" aria-labelledby="tab-event" <?= $bookTab === 'event' ? '' : 'hidden' ?>>
    <?= $formHead('event') ?>
    <div class="fgrid">
      <div class="f"><label for="e-t">Occasion</label><select id="e-t" name="item_name"><option>Birthday party</option><option>Family function</option><option>Engagement / small wedding</option><option>Corporate meet</option><option>Personal celebration</option><option>Other</option></select></div>
      <div class="f"><label for="e-d">Date</label><input type="date" id="e-d" name="date_from" required></div>
      <div class="f"><label for="e-g">Guests</label><input type="number" id="e-g" name="guests" min="2" max="500" value="20" required></div>
      <div class="f"><label for="e-p">Phone / WhatsApp</label><input id="e-p" name="phone" type="tel" inputmode="tel" autocomplete="tel" required></div>
      <div class="f f--6"><label for="e-n">Your name</label><input id="e-n" name="name" autocomplete="name" required></div>
      <div class="f f--6"><label for="e-no">Plans & menu ideas (optional)</label><input id="e-no" name="notes" placeholder="Decoration, veg / non-veg, music…"></div>
    </div>
    <div class="formfoot"><p><?= icon('party', 18) ?><span><?= et('book.event.note', 'Party and personal functions are both available. We will call to discuss menu, space and budget.') ?></span></p>
      <button class="btn btn--gold" type="submit">Plan my event <?= icon('arrow-right', 18) ?></button></div>
    </form>
  </section>

  <!-- ENQUIRY -->
  <section class="panel" id="panel-enquiry" role="tabpanel" aria-labelledby="tab-enquiry" <?= $bookTab === 'enquiry' ? '' : 'hidden' ?>>
    <?= $formHead('enquiry') ?>
    <div class="fgrid">
      <div class="f f--4"><label for="q-n">Your name</label><input id="q-n" name="name" autocomplete="name" required></div>
      <div class="f f--4"><label for="q-p">Phone / WhatsApp</label><input id="q-p" name="phone" type="tel" inputmode="tel" autocomplete="tel" required></div>
      <div class="f f--4"><label for="q-e">Email (optional)</label><input id="q-e" name="email" type="email" autocomplete="email"></div>
      <div class="f f--12"><label for="q-m">How can we help?</label><textarea id="q-m" name="notes" required placeholder="Dates, number of guests, anything you would like to know…"></textarea></div>
    </div>
    <div class="formfoot"><p><?= icon('message', 18) ?><span><?= et('book.enquiry.note', 'We usually reply the same day.') ?></span></p>
      <button class="btn btn--gold" type="submit">Send enquiry <?= icon('send', 18) ?></button></div>
    </form>
  </section>
</div>
