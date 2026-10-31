<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_attractions', 'active' => 'attractions', 'title' => 'Explore Jawai', 'singular' => 'place', 'plural' => 'places', 'thumb' => 'image', 'order' => 'sort_order, id',
  'intro' => 'Things to see and do around Jawai',
  'fields' => [
    'name' => ['Name', 'text', '', null, 6], 'category' => ['Type', 'select', 'Groups the guide page.', ['Wildlife' => 'Wildlife', 'Nature' => 'Nature', 'Culture' => 'Culture', 'Heritage' => 'Heritage', 'Experience' => 'Experience'], 3], 'status' => ['Visibility', 'select', '', ['active' => 'Visible', 'hidden' => 'Hidden'], 3],
    'distance' => ['Distance / time', 'text', 'e.g. Short drive, Approx. 2 hrs', null, 6], 'sort_order' => ['Display order', 'number', '', null, 6],
    'description' => ['Description', 'textarea'], 'image' => ['Photo', 'image'],
  ],
  'defaults' => ['status' => 'active', 'category' => 'Nature', 'sort_order' => 50],
  'list' => [['Place', fn($r) => '<strong>' . e($r['name']) . '</strong>'], ['Type', 'category'], ['Distance', 'distance'], ['Status', fn($r) => $r['status'] === 'active' ? pill_status('Confirmed') : pill_status('hidden')]],
]);
