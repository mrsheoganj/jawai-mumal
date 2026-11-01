<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_slides', 'active' => 'slides', 'title' => 'Home banner slides', 'singular' => 'slide', 'thumb' => 'image', 'order' => 'sort_order, id', 'required' => 'image',
  'intro' => 'One slide = static banner; two or more = gentle automatic slideshow',
  'fields' => [
    'image' => ['Banner photo (wide landscape, at least 1600 px)', 'image'],
    'eyebrow' => ['Small line above the title', 'text', '', null, 6], 'title' => ['Title', 'text', '', null, 6],
    'text' => ['Short description', 'textarea'],
    'cta_label' => ['Main button text', 'text', '', null, 3], 'cta_url' => ['Main button link', 'text', 'e.g. #book, safari.php, restaurant.php or a full https:// link', null, 3],
    'cta2_label' => ['Second button text', 'text', 'Optional', null, 3], 'cta2_url' => ['Second button link', 'text', '', null, 3],
    'status' => ['Show this slide', 'select', '', ['active' => 'Visible', 'hidden' => 'Hidden'], 6], 'sort_order' => ['Order', 'number', 'Lower numbers first.', null, 6],
  ],
  'rules' => function (array $v) { $e = []; foreach (['cta_url' => 'Main button link', 'cta2_url' => 'Second button link'] as $k => $l) { $u = trim((string)($v[$k] ?? '')); if ($u !== '' && !preg_match('~^(#[\w-]+|[a-z0-9_\-/]+(\.php)?(\?[\w=&%\-]*)?(#[\w-]+)?|https?://\S+)$~i', $u)) $e[] = $l . ' must be like #book, safari, or a full https:// link.'; } return $e; },
  'defaults' => ['status' => 'active', 'sort_order' => 10, 'cta_label' => 'Book your stay', 'cta_url' => '#book'],
  'list' => [['Slide', fn($r) => '<strong>' . e($r['title']) . '</strong><br><span class="muted">' . e($r['eyebrow']) . '</span>'], ['Button', 'cta_label'], ['Order', 'sort_order'], ['Status', fn($r) => $r['status'] === 'active' ? pill_status('Confirmed') : pill_status('hidden')]],
]);
