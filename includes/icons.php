<?php
/**
 * Icons come from professional, open-source packs (not hand drawn):
 *   - Lucide (ISC licence, https://lucide.dev)             – interface / amenity icons, 24px stroke grid
 *   - Simple Icons (CC0, https://simpleicons.org)           – WhatsApp / Instagram / Facebook brand logos
 * includes/icons_data.php is generated from the packs. Usage: echo icon('bed', 20);
 */
function icon_set(): array {
    static $d = null;
    if ($d === null) $d = require __DIR__ . '/icons_data.php';
    return $d;
}
/** Inner SVG markup of stroke icons (used inside other SVGs, e.g. the distance wheel). */
function icon_paths(): array {
    static $p = null;
    if ($p === null) { $p = []; foreach (icon_set() as $k => $i) { if ($i['t'] === 's') $p[$k] = $i['d']; } }
    return $p;
}
function icon(string $name, int $size = 20, string $class = ''): string {
    $set = icon_set();
    if (!isset($set[$name])) return '';
    $i = $set[$name];
    $cls = 'i' . ($class !== '' ? ' ' . $class : '');
    $attr = $i['t'] === 'f'
        ? 'fill="currentColor"'
        : 'fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"';
    return '<svg class="' . $cls . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" ' . $attr . ' aria-hidden="true" focusable="false">' . $i['d'] . '</svg>';
}
