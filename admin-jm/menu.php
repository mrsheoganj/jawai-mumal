<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_menu', 'active' => 'menu', 'title' => 'Restaurant menu', 'singular' => 'dish', 'plural' => 'dishes', 'thumb' => 'image', 'order' => 'category, sort_order, id',
  'fields' => [
    'name' => ['Dish name', 'text', '', null, 6], 'category' => ['Category', 'text', 'e.g. Rajasthani Classics, Desserts, Café & Drinks', null, 6],
    'price' => ['Price (₹)', 'number', 'Between ₹0 and ₹50,000.', null, 3, ['min' => 0, 'max' => 50000]], 'available' => ['Available today', 'check', 'Untick to hide while out of stock.', null, 3],
    'sort_order' => ['Display order', 'number', '', null, 3], 'spice' => ['Spice level', 'text', 'Mild / Medium / Hot (optional)', null, 6], 'tag' => ['Badge', 'text', 'e.g. Signature, Popular (optional)', null, 6],
    'description' => ['Description', 'textarea'], 'image' => ['Photo (optional)', 'image'],
  ],
  'defaults' => ['veg' => 1, 'available' => 1, 'price' => 200],
  'list' => [['Dish', fn($r) => '<strong>' . e($r['name']) . '</strong><br><span class="muted">' . 'Veg' . '</span>'], ['Category', 'category'], ['Price', fn($r) => money($r['price'])], ['Status', fn($r) => $r['available'] ? pill_status('Confirmed') : pill_status('hidden')]],
]);
