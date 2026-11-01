<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_slides', 'active' => 'slides', 'title' => 'Home banner slides', 'singular' => 'slide', 'thumb' => 'image', 'order' => 'sort_order, id', 'required' => 'image',
  'intro' => 'One slide = static banner; two or more = gentle automatic slideshow',
  'add_query' => in_array($_GET['device'] ?? '', ['desktop', 'mobile'], true) ? '&device=' . $_GET['device'] : '',
  'where' => function () { $d = $_GET['device'] ?? ''; return in_array($d, ['desktop', 'mobile'], true) ? ["device IN (?, 'both')", [$d]] : ['', []]; },
  'top' => function () { $d = $_GET['device'] ?? ''; $n = fn($k) => (int)val("SELECT COUNT(*) FROM jm_slides WHERE status='active' AND device IN (?, 'both')", [$k]); ?>
    <div class="card" style="margin-bottom:1rem">
      <h2 style="margin-top:0">Choose which banner you are editing</h2>
      <div class="actions" style="flex-wrap:wrap;gap:.6rem">
        <a class="btn <?= $d === 'desktop' ? 'btn--gold' : 'btn--line' ?>" href="slides.php?device=desktop"><?= icon('globe', 18) ?> Computers &amp; big screens (<?= $n('desktop') ?>)</a>
        <a class="btn <?= $d === 'mobile' ? 'btn--gold' : 'btn--line' ?>" href="slides.php?device=mobile"><?= icon('menu', 18) ?> Phones (<?= $n('mobile') ?>)</a>
        <a class="btn <?= $d === '' ? 'btn--gold' : 'btn--line' ?>" href="slides.php">All slides</a>
      </div>
      <p class="help" style="margin-top:.8rem"><strong>Computers &amp; big screens:</strong> wide landscape photos, about 2400 × 1350 px (16:9). <strong>Phones:</strong> tall portrait photos, about 1080 × 1620 px (2:3), with the subject in the middle. Each slide has a “Show on” setting: only computers, only phones, or both. If one kind has no slides, it uses the other kind’s slides.</p>
    </div>
  <?php },
  'fields' => [
    'device' => ['Show on', 'select', 'Phones and computers can have different photos and text.', ['both' => 'Phones and computers', 'desktop' => 'Computers & big screens only', 'mobile' => 'Phones only'], 12],
    'image' => ['Banner photo', 'image', 'Computers: landscape 2400 × 1350 px. Phones: portrait 1080 × 1620 px. Under 400 KB (JPG or WebP) loads fastest.'],
    'eyebrow' => ['Small line above the title', 'text', '', null, 6], 'title' => ['Title', 'text', '', null, 6],
    'text' => ['Short description', 'textarea'],
    'cta_label' => ['Main button text', 'text', '', null, 3], 'cta_url' => ['Main button link', 'text', 'e.g. #book, safari.php, restaurant.php or a full https:// link', null, 3],
    'cta2_label' => ['Second button text', 'text', 'Optional', null, 3], 'cta2_url' => ['Second button link', 'text', '', null, 3],
    'status' => ['Show this slide', 'select', '', ['active' => 'Visible', 'hidden' => 'Hidden'], 6], 'sort_order' => ['Order', 'number', 'Lower numbers first.', null, 6],
  ],
  'rules' => function (array $v) { $e = []; foreach (['cta_url' => 'Main button link', 'cta2_url' => 'Second button link'] as $k => $l) { $u = trim((string)($v[$k] ?? '')); if ($u !== '' && !preg_match('~^(#[\w-]+|[a-z0-9_\-/]+(\.php)?(\?[\w=&%\-]*)?(#[\w-]+)?|https?://\S+)$~i', $u)) $e[] = $l . ' must be like #book, safari, or a full https:// link.'; } return $e; },
  'defaults' => ['device' => in_array($_GET['device'] ?? '', ['desktop', 'mobile'], true) ? $_GET['device'] : 'both', 'status' => 'active', 'sort_order' => 10, 'cta_label' => 'Book your stay', 'cta_url' => '#book'],
  'list' => [['Slide', fn($r) => '<strong>' . e($r['title']) . '</strong><br><span class="muted">' . e($r['eyebrow']) . '</span>'], ['Button', 'cta_label'], ['Shows on', fn($r) => ['both' => 'Phones + computers', 'desktop' => 'Computers only', 'mobile' => 'Phones only'][$r['device'] ?: 'both'] ?? 'Both'], ['Order', 'sort_order'], ['Status', fn($r) => $r['status'] === 'active' ? pill_status('Confirmed') : pill_status('hidden')]],
]);
