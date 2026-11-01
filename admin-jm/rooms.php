<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_rooms', 'active' => 'rooms', 'title' => 'Rooms', 'singular' => 'room', 'slug' => 'name', 'thumb' => 'image', 'order' => 'sort_order, id',
  'intro' => 'Change names, prices, photos and details any time',
  'fields' => [
    'name' => ['Room name', 'text', '', null, 6], 'category' => ['Category (e.g. Deluxe, Suite)', 'text', 'Used for the filter buttons.', null, 3],
    'status' => ['Visibility', 'select', 'Hidden rooms disappear from the website.', ['active' => 'Visible', 'hidden' => 'Hidden'], 3],
    'tagline' => ['Short tagline', 'text', '', null, 12],
    'price' => ['Price per night (₹)', 'number', 'Before GST. Between ₹500 and ₹500,000.', null, 3, ['min' => 500, 'max' => 500000]], 'inventory' => ['Number of rooms of this type', 'number', '', null, 3],
    'size' => ['Room size', 'text', 'e.g. 260 sq ft', null, 3], 'sort_order' => ['Display order', 'number', 'Lower numbers appear first.', null, 3],
    'capacity' => ['Capacity', 'text', 'e.g. 2 adults + 1 child', null, 4], 'beds' => ['Beds', 'text', 'e.g. 1 King bed', null, 4], 'view_type' => ['View', 'text', 'e.g. Hill view', null, 4],
    'featured' => ['Show on the home page', 'check', '', null, 12],
    'image' => ['Main photo', 'image'], 'gallery' => ['More photos', 'gallery'],
    'description' => ['Description', 'textarea'], 'amenities' => ['Amenities (one per line)', 'textarea', 'Each line becomes a tick-list item on the room page.'],
  ],
  'defaults' => ['status' => 'active', 'price' => 3000, 'inventory' => 2, 'sort_order' => 10, 'featured' => 0],
  'list' => [['Room', fn($r) => '<strong>' . e($r['name']) . '</strong><br><span class="muted">' . e($r['category']) . '</span>'], ['Price / night', fn($r) => money($r['price'])], ['Rooms', 'inventory'], ['Status', fn($r) => pill_status($r['status'] === 'active' ? 'Confirmed' : 'hidden') . ($r['featured'] ? ' <span class="pill">Home</span>' : '')]],
]);
