<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_safaris', 'active' => 'safaris', 'title' => 'Safari packages', 'singular' => 'safari', 'slug' => 'title', 'thumb' => 'image', 'order' => 'sort_order, id',
  'required' => 'title',
  'fields' => [
    'title' => ['Safari name', 'text', '', null, 6], 'badge' => ['Badge', 'text', 'e.g. Most popular (optional)', null, 3], 'status' => ['Visibility', 'select', '', ['active' => 'Visible', 'hidden' => 'Hidden'], 3],
    'price_pp' => ['Price per person (₹)', 'number', 'Shared jeep.', null, 3], 'private_price' => ['Private jeep price (₹)', 'number', 'Whole jeep.', null, 3],
    'duration' => ['Duration', 'text', 'e.g. 3 hours', null, 3], 'timing' => ['Timing', 'text', 'e.g. 5:30 AM – 8:30 AM', null, 3],
    'vehicle' => ['Vehicle', 'text', '', null, 4], 'difficulty' => ['Difficulty', 'text', 'Easy / Moderate', null, 4], 'sort_order' => ['Display order', 'number', '', null, 4],
    'description' => ['Description', 'textarea'], 'highlights' => ['Highlights (one per line)', 'textarea'], 'image' => ['Photo', 'image'],
  ],
  'defaults' => ['status' => 'active', 'price_pp' => 3000, 'private_price' => 10000, 'sort_order' => 10],
  'list' => [['Safari', fn($r) => '<strong>' . e($r['title']) . '</strong><br><span class="muted">' . e($r['timing']) . '</span>'], ['Per person', fn($r) => money($r['price_pp'])], ['Private jeep', fn($r) => money($r['private_price'])], ['Status', fn($r) => $r['status'] === 'active' ? pill_status('Confirmed') : pill_status('hidden')]],
]);
