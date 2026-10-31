<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_routes', 'active' => 'routes', 'title' => 'Distances & travel', 'singular' => 'place', 'plural' => 'places', 'order' => 'sort_order, id', 'required' => 'name',
  'intro' => 'Airports, railway stations and cities shown in the “How to reach” map',
  'fields' => [
    'name' => ['Place name', 'text', 'e.g. Udaipur Airport (Maharana Pratap)', null, 6],
    'kind' => ['Type', 'select', 'Chooses the icon.', ['airport' => 'Airport', 'rail' => 'Railway station', 'city' => 'City', 'bus' => 'Bus stand', 'other' => 'Other'], 3],
    'code' => ['Short code', 'text', 'e.g. UDR, JWB (optional)', null, 3],
    'km' => ['Distance to the hotel (km)', 'number', 'Road distance.', null, 3],
    'travel_time' => ['Travel time', 'text', 'e.g. about 2.5 hrs', null, 3],
    'sort_order' => ['Order', 'number', '', null, 3], 'status' => ['Visibility', 'select', '', ['active' => 'Visible', 'hidden' => 'Hidden'], 3],
    'note' => ['Short note', 'text', 'Optional, e.g. Nearest railway station', null, 12],
    'lat' => ['Latitude', 'text', 'Used to build the “Directions” link. Paste from Google Maps (e.g. 24.6177). Leave empty to search by the place name.', null, 6],
    'lng' => ['Longitude', 'text', 'e.g. 73.8961', null, 6],
  ],
  'defaults' => ['kind' => 'rail', 'status' => 'active', 'sort_order' => 50, 'km' => 50],
  'list' => [['Place', fn($r) => '<strong>' . e($r['name']) . '</strong>' . ($r['code'] ? ' <span class="pill">' . e($r['code']) . '</span>' : '') . '<br><span class="muted">' . e(route_kind_label($r['kind'])) . '</span>'], ['Distance', fn($r) => rtrim(rtrim(number_format((float)$r['km'], 1), '0'), '.') . ' km<br><span class="muted">' . e($r['travel_time']) . '</span>'], ['Coordinates', fn($r) => has_coords($r['lat'], $r['lng']) ? e($r['lat'] . ', ' . $r['lng']) : '<span class="muted">searches by name</span>'], ['Status', fn($r) => $r['status'] === 'active' ? pill_status('Confirmed') : pill_status('hidden')]],
]);
