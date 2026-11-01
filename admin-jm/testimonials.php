<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_testimonials', 'active' => 'testimonials', 'title' => 'Guest reviews', 'singular' => 'review', 'order' => 'sort_order, id', 'required' => 'name',
  'intro' => 'Only “Visible” reviews appear on the website — add real guest feedback only',
  'fields' => [
    'name' => ['Guest name', 'text', '', null, 4], 'origin' => ['City / country', 'text', '', null, 4], 'rating' => ['Rating (1–5)', 'number', '', null, 2, ['min' => 1, 'max' => 5]], 'sort_order' => ['Order', 'number', '', null, 2],
    'status' => ['Show on website', 'select', '', ['visible' => 'Visible', 'hidden' => 'Hidden'], 6], 'quote' => ['Review text', 'textarea'],
  ],
  'defaults' => ['rating' => 5, 'status' => 'hidden', 'sort_order' => 10],
  'list' => [['Guest', fn($r) => '<strong>' . e($r['name']) . '</strong><br><span class="muted">' . e($r['origin']) . '</span>'], ['Review', fn($r) => e(mb_substr($r['quote'], 0, 90))], ['Rating', 'rating'], ['Status', fn($r) => $r['status'] === 'visible' ? pill_status('Confirmed') : pill_status('hidden')]],
]);
