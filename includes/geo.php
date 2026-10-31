<?php
/** Geo helpers: real distances, compass bearings, road routing (OSRM) and place search (Nominatim, OpenStreetMap data). */

function geo_haversine_km(float $lat1, float $lng1, float $lat2, float $lng2): float {
    $r = 6371.0088; $p = M_PI / 180;
    $a = sin(($lat2 - $lat1) * $p / 2) ** 2 + cos($lat1 * $p) * cos($lat2 * $p) * sin(($lng2 - $lng1) * $p / 2) ** 2;
    return 2 * $r * asin(min(1, sqrt($a)));
}
/** Initial compass bearing in degrees (0 = north) from point 1 to point 2. */
function geo_bearing(float $lat1, float $lng1, float $lat2, float $lng2): float {
    $p = M_PI / 180; $y = sin(($lng2 - $lng1) * $p) * cos($lat2 * $p);
    $x = cos($lat1 * $p) * sin($lat2 * $p) - sin($lat1 * $p) * cos($lat2 * $p) * cos(($lng2 - $lng1) * $p);
    return fmod(atan2($y, $x) / $p + 360, 360);
}
function geo_compass(float $deg): string {
    return ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'][(int)round($deg / 22.5) % 16];
}
function geo_fmt_time(int $minutes): string {
    $h = intdiv($minutes, 60); $m = $minutes % 60;
    if ($h === 0) return 'about ' . $m . ' min';
    return 'about ' . $h . ' hr' . ($m >= 8 ? ' ' . (int)(round($m / 5) * 5) . ' min' : '');
}
function geo_http(string $url): ?string {
    $ua = 'JawaiMumalSite/1.0 (' . (setting('email') ?: 'website') . ')';
    if (function_exists('curl_init')) {
        $c = curl_init($url);
        curl_setopt_array($c, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_USERAGENT => $ua, CURLOPT_FOLLOWLOCATION => true]);
        if (($ca = getenv('CURL_CA_BUNDLE')) && is_file($ca)) curl_setopt($c, CURLOPT_CAINFO, $ca);   // custom CA (e.g. corporate proxy)
        $r = curl_exec($c); curl_close($c);
        return $r !== false ? $r : null;
    }
    $r = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 20, 'header' => "User-Agent: $ua\r\n"]]));
    return $r !== false ? $r : null;
}
/** Real driving route: returns ['km'=>float,'minutes'=>int,'geom'=>[[lng,lat],...]] or null. Uses the public OSRM server (OpenStreetMap roads). */
function geo_route(float $lat1, float $lng1, float $lat2, float $lng2): ?array {
    $j = geo_http("https://router.project-osrm.org/route/v1/driving/$lng1,$lat1;$lng2,$lat2?overview=simplified&geometries=geojson");
    $d = $j ? json_decode($j, true) : null;
    if (!$d || ($d['code'] ?? '') !== 'Ok' || empty($d['routes'][0])) return null;
    $r = $d['routes'][0];
    $geom = array_map(fn($p) => [round($p[0], 4), round($p[1], 4)], $r['geometry']['coordinates'] ?? []);
    return ['km' => round($r['distance'] / 1000, 1), 'minutes' => (int)round($r['duration'] / 60 * 1.2), 'geom' => $geom]; // +20% for traffic and village roads
}
/** Find coordinates for a place name (OpenStreetMap Nominatim). Tries a few variants of the name. Returns ['lat'=>,'lng'=>,'label'=>] or null. */
function geo_search(string $q): ?array {
    $tries = array_values(array_unique(array_filter([$q, trim(preg_replace('/\s*\(.*?\)/', '', $q)), trim(preg_replace('/\b(Jain|Temple|Railway|Station|Airport|Fort|Palace)\b/i', '', $q)) . ', Rajasthan, India', $q . ', Rajasthan, India'])));
    foreach ($tries as $n => $t) {
        if ($n) usleep(1100000);
        $j = geo_http('https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=in&q=' . rawurlencode($t));
        $d = $j ? json_decode($j, true) : null;
        if (!empty($d[0])) return ['lat' => $d[0]['lat'], 'lng' => $d[0]['lon'], 'label' => $d[0]['display_name'] ?? $q];
    }
    return null;
}
