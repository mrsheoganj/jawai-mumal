<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php'; require_once __DIR__ . '/../includes/geo.php';

function route_fill(array $r): string {   // returns a status message
    $hl = setting('map_lat'); $hg = setting('map_lng');
    if (!has_coords($hl, $hg)) return 'Set the hotel latitude/longitude first (Contact, logo & rules).';
    if (!has_coords($r['lat'], $r['lng'])) {
        $f = geo_search($r['name']);
        if (!$f) return 'Could not find “' . $r['name'] . '” — enter its coordinates by hand.';
        $r['lat'] = $f['lat']; $r['lng'] = $f['lng'];
        q('UPDATE jm_routes SET lat=?, lng=? WHERE id=?', [$f['lat'], $f['lng'], $r['id']]);
    }
    $g = geo_route((float)$hl, (float)$hg, (float)$r['lat'], (float)$r['lng']);
    if (!$g) return 'The routing service did not answer for “' . $r['name'] . '”. Try again in a minute.';
    q('UPDATE jm_routes SET km=?, travel_time=?, geom=? WHERE id=?', [$g['km'], geo_fmt_time($g['minutes']), json_encode($g['geom']), $r['id']]);
    return '';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'] ?? '', ['recalc', 'recalc_one'], true)) {
    $one = ($_POST['action'] === 'recalc_one') ? [(int)$_POST['id']] : array_column(rows("SELECT id FROM jm_routes WHERE status='active'"), 'id');
    $ok = 0; $bad = [];
    foreach ($one as $id) {
        $r = row('SELECT * FROM jm_routes WHERE id = ?', [$id]); if (!$r) continue;
        $m = route_fill($r); if ($m === '') $ok++; else $bad[] = $m; usleep(1100000); // be polite to the free OpenStreetMap services
    }
    if ($ok) flash('ok', $ok . ' route(s) updated with real road distance, time and path.');
    foreach (array_unique($bad) as $m) flash('err', $m);
    redirect('routes.php');
}
admin_crud([
  'table' => 'jm_routes', 'active' => 'routes', 'title' => 'Distances & travel', 'singular' => 'place', 'plural' => 'places', 'order' => 'sort_order, id', 'required' => 'name',
  'intro' => 'Airports, stations and cities shown on the “How to reach” map',
  'top' => function () { $has = has_coords(setting('map_lat'), setting('map_lng')); ?>
    <div class="card"><h2>Real distances</h2>
      <p class="muted" style="margin-top:0">Hotel position: <strong><?= $has ? e(setting('map_lat') . ', ' . setting('map_lng')) : 'not set' ?></strong> · <a href="content.php">change coordinates</a>.
      Add a place with its name (and coordinates if you have them), then press <strong>Calculate</strong>. The site finds the place on OpenStreetMap, measures the real driving route from your hotel, and saves the distance, drive time and road path for the map. Drive times include about 20% extra for traffic and village roads.</p>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="recalc"><button class="btn btn--gold btn--sm" data-confirm="This contacts the free OpenStreetMap services and can take a few seconds per place. Continue?"><?= icon('refresh', 16) ?> Recalculate all real distances</button></form></div>
  <?php },
  'row_actions' => fn($r) => '<form method="post" style="display:inline">' . csrf_field() . '<input type="hidden" name="action" value="recalc_one"><input type="hidden" name="id" value="' . (int)$r['id'] . '"><button class="btn btn--line btn--sm" title="Find on the map and measure the real road distance">' . icon('refresh', 16) . ' Calculate</button></form>',
  'fields' => [
    'name' => ['Place name', 'text', 'e.g. Udaipur Airport (Maharana Pratap)', null, 6],
    'kind' => ['Type', 'select', 'Chooses the icon.', ['airport' => 'Airport', 'rail' => 'Railway station', 'city' => 'City', 'bus' => 'Bus stand', 'other' => 'Other'], 3],
    'code' => ['Short code', 'text', 'e.g. UDR, JWB (optional)', null, 3],
    'km' => ['Road distance to the hotel (km)', 'number', 'Filled automatically by “Calculate”. You can also type it.', null, 3],
    'travel_time' => ['Travel time', 'text', 'e.g. about 2 hr 20 min', null, 3],
    'sort_order' => ['Order', 'number', '', null, 3], 'status' => ['Visibility', 'select', '', ['active' => 'Visible', 'hidden' => 'Hidden'], 3],
    'note' => ['Short note', 'text', 'Optional, e.g. Nearest railway station', null, 12],
    'lat' => ['Latitude', 'text', 'Right-click the place in Google Maps and copy the first number. Or leave empty and press “Calculate” to look it up by name.', null, 6],
    'lng' => ['Longitude', 'text', 'The second number.', null, 6],
  ],
  'defaults' => ['kind' => 'rail', 'status' => 'active', 'sort_order' => 50, 'km' => 0],
  'list' => [['Place', fn($r) => '<strong>' . e($r['name']) . '</strong>' . ($r['code'] ? ' <span class="pill">' . e($r['code']) . '</span>' : '') . '<br><span class="muted">' . e(route_kind_label($r['kind'])) . '</span>'], ['Road distance', fn($r) => rtrim(rtrim(number_format((float)$r['km'], 1), '0'), '.') . ' km<br><span class="muted">' . e($r['travel_time']) . '</span>'], ['Map', fn($r) => has_coords($r['lat'], $r['lng']) ? e($r['lat'] . ', ' . $r['lng']) . '<br><span class="muted">' . ($r['geom'] ? 'real road path saved' : 'straight line only') . '</span>' : '<span class="muted">no coordinates yet</span>'], ['Status', fn($r) => $r['status'] === 'active' ? pill_status('Confirmed') : pill_status('hidden')]],
]);
