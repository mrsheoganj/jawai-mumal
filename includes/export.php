<?php
/**
 * Export helpers – real Excel (.xlsx) and PDF files with no external libraries.
 *
 * $cols is a list of column specs:  ['Heading', width (chars), type]   type: 's' text, 'n' number, 'm' money (Rs, thousands separators), 'c' centred text
 * $rows is a list of rows (plain arrays in the same order as $cols).
 */

function export_filename(string $base, string $ext): string { return preg_replace('/[^a-z0-9-]+/i', '-', $base) . '-' . date('Y-m-d') . '.' . $ext; }
function export_clean($v): string { return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string)$v); }

// ======================================================================= XLSX
function xlsx_col(int $i): string { $s = ''; for ($i++; $i > 0; $i = intdiv($i - 1, 26)) $s = chr(65 + ($i - 1) % 26) . $s; return $s; }
function xlsx_esc(string $s): string { return htmlspecialchars(export_clean($s), ENT_XML1 | ENT_QUOTES, 'UTF-8'); }

function export_xlsx(string $file, string $sheetName, string $title, string $subtitle, array $cols, array $rows): void {
    $n = count($cols); $last = xlsx_col($n - 1); $hr = 4; $first = $hr + 1; $end = $hr + max(1, count($rows));
    // styles: 0 default · 1 header · 2/3 text (plain/zebra) · 4/5 money · 6 title · 7 subtitle · 8/9 centred · 10/11 number
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
      . '<numFmts count="1"><numFmt numFmtId="164" formatCode="&quot;₹&quot;#,##0"/></numFmts>'
      . '<fonts count="4"><font><sz val="10"/><name val="Arial"/></font><font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Arial"/></font><font><b/><sz val="16"/><color rgb="FF2B180E"/><name val="Georgia"/></font><font><i/><sz val="9"/><color rgb="FF7A6A5C"/><name val="Arial"/></font></fonts>'
      . '<fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF2B180E"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF6F1E7"/><bgColor indexed="64"/></patternFill></fill></fills>'
      . '<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFDDD3C4"/></left><right style="thin"><color rgb="FFDDD3C4"/></right><top style="thin"><color rgb="FFDDD3C4"/></top><bottom style="thin"><color rgb="FFDDD3C4"/></bottom><diagonal/></border></borders>'
      . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
      . '<cellXfs count="12">'
      . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
      . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
      . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
      . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
      . '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
      . '<xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="top"/></xf>'
      . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
      . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
      . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top" wrapText="1"/></xf>'
      . '<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top" wrapText="1"/></xf>'
      . '<xf numFmtId="1" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top"/></xf>'
      . '<xf numFmtId="1" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top"/></xf>'
      . '</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';

    $cell = function (string $ref, string $text, int $style): string { return '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">' . xlsx_esc($text) . '</t></is></c>'; };
    $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
      . '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
      . '<sheetViews><sheetView workbookViewId="0" showGridLines="0"><pane ySplit="' . $hr . '" topLeftCell="A' . $first . '" activePane="bottomLeft" state="frozen"/><selection pane="bottomLeft" activeCell="A' . $first . '" sqref="A' . $first . '"/></sheetView></sheetViews>'
      . '<sheetFormatPr defaultRowHeight="15"/><cols>';
    foreach ($cols as $i => $c) $xml .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . (float)($c[1] ?? 14) . '" customWidth="1"/>';
    $xml .= '</cols><sheetData>';
    $xml .= '<row r="1" ht="26" customHeight="1">' . $cell('A1', $title, 6) . '</row>';
    $xml .= '<row r="2">' . $cell('A2', $subtitle, 7) . '</row><row r="3"/>';
    $xml .= '<row r="' . $hr . '" ht="30" customHeight="1">';
    foreach ($cols as $i => $c) $xml .= $cell(xlsx_col($i) . $hr, (string)$c[0], 1);
    $xml .= '</row>';
    foreach ($rows as $ri => $r) {
        $rn = $first + $ri; $z = $ri % 2; $xml .= '<row r="' . $rn . '">';
        foreach ($cols as $i => $c) {
            $v = $r[$i] ?? ''; $ref = xlsx_col($i) . $rn; $t = $c[2] ?? 's';
            if (($t === 'm' || $t === 'n') && $v !== '' && is_numeric($v)) $xml .= '<c r="' . $ref . '" s="' . (($t === 'm' ? 4 : 10) + $z) . '"><v>' . (0 + $v) . '</v></c>';
            else $xml .= $cell($ref, (string)$v, ($t === 'c' ? 8 : 2) + $z);
        }
        $xml .= '</row>';
    }
    if (!$rows) $xml .= '<row r="' . $first . '">' . $cell('A' . $first, 'No records for this selection.', 2) . '</row>';
    $xml .= '</sheetData><autoFilter ref="A' . $hr . ':' . $last . $end . '"/>'
      . '<pageMargins left="0.4" right="0.4" top="0.6" bottom="0.6" header="0.3" footer="0.3"/><pageSetup paperSize="9" orientation="landscape" fitToWidth="1" fitToHeight="0"/>'
      . '<headerFooter><oddFooter>&amp;L' . xlsx_esc(setting('site_name')) . '&amp;RPage &amp;P of &amp;N</oddFooter></headerFooter></worksheet>';
    $sn = xlsx_esc(mb_substr(preg_replace('/[\\\\\/?*\[\]:]/', ' ', $sheetName), 0, 31));
    $parts = [
      '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/></Types>',
      '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/></Relationships>',
      'docProps/core.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:title>' . xlsx_esc($title) . '</dc:title><dc:creator>' . xlsx_esc(setting('site_name')) . '</dc:creator><dcterms:created xsi:type="dcterms:W3CDTF">' . gmdate('Y-m-d\TH:i:s\Z') . '</dcterms:created></cp:coreProperties>',
      'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><bookViews><workbookView/></bookViews><sheets><sheet name="' . $sn . '" sheetId="1" r:id="rId1"/></sheets><definedNames><definedName name="_xlnm._FilterDatabase" localSheetId="0" hidden="1">\'' . $sn . '\'!$A$' . $hr . ':$' . $last . '$' . $end . '</definedName><definedName name="_xlnm.Print_Titles" localSheetId="0">\'' . $sn . '\'!$' . $hr . ':$' . $hr . '</definedName></definedNames></workbook>',
      'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>',
      'xl/styles.xml' => $styles, 'xl/worksheets/sheet1.xml' => $xml,
    ];
    if (!class_exists('ZipArchive')) { http_response_code(500); exit('Excel export needs the PHP zip extension (enable it in hPanel → PHP Configuration).'); }
    $tmp = tempnam(sys_get_temp_dir(), 'jmx'); $z = new ZipArchive();
    if ($z->open($tmp, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true) { http_response_code(500); exit('Could not create the Excel file.'); }
    foreach ($parts as $name => $data) $z->addFromString($name, $data);
    $z->close();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Content-Length: ' . filesize($tmp)); header('Cache-Control: no-store'); readfile($tmp); @unlink($tmp); exit;
}

// ======================================================================= PDF
function pdf_w(string $s, float $size, bool $bold = false): float {
    static $w = null;
    if ($w === null) { $w = [278,278,355,556,556,889,667,191,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,278,278,584,584,584,556,1015,667,667,722,722,667,611,778,722,278,500,667,556,833,722,778,667,778,722,667,611,722,667,944,667,667,611,278,278,278,469,556,333,556,556,500,556,556,278,556,556,222,222,500,222,833,556,556,556,556,333,500,278,556,500,722,500,500,500,334,260,334,584]; }
    $t = 0; $n = strlen($s); for ($i = 0; $i < $n; $i++) { $o = ord($s[$i]); $t += ($o >= 32 && $o <= 126) ? $w[$o - 32] : 556; }
    return $t * $size / 1000 * ($bold ? 1.07 : 1);
}
/** UTF-8 -> the PDF's WinAnsi characters (the ₹ sign is not in the standard fonts, so it becomes "Rs"). */
function pdf_txt(string $s): string {
    $s = export_clean($s); $s = strtr($s, ['₹' => 'Rs ', '→' => '->', '•' => '·', "\r" => '', "\t" => ' ']);
    $s = str_replace('Rs )', 'Rs)', $s);
    $s = function_exists('mb_convert_encoding') ? mb_convert_encoding($s, 'Windows-1252', 'UTF-8') : utf8_decode($s);
    return $s;
}
function pdf_esc(string $s): string { return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s); }
function pdf_wrap(string $s, float $width, float $size, bool $bold = false): array {
    $out = [];
    foreach (explode("\n", pdf_txt($s)) as $para) {
        $line = '';
        foreach (preg_split('/ +/', trim($para)) as $word) {
            while (pdf_w($word, $size, $bold) > $width && strlen($word) > 1) {   // very long word: split it
                $k = strlen($word); while ($k > 1 && pdf_w(substr($word, 0, $k), $size, $bold) > $width) $k--;
                if ($line !== '') { $out[] = $line; $line = ''; }
                $out[] = substr($word, 0, $k); $word = substr($word, $k);
            }
            $try = $line === '' ? $word : $line . ' ' . $word;
            if ($line !== '' && pdf_w($try, $size, $bold) > $width) { $out[] = $line; $line = $word; } else $line = $try;
        }
        $out[] = $line;
    }
    return $out ?: [''];
}

function export_pdf(string $file, string $title, string $subtitle, array $cols, array $rows): void {
    $PW = 842; $PH = 595; $M = 28; $size = 7.6; $lead = 9.6; $pad = 3.2; $fsH = 7.8;
    $avail = $PW - 2 * $M; $sum = array_sum(array_map(fn($c) => (float)($c[1] ?? 12), $cols)) ?: 1;
    $cw = []; foreach ($cols as $c) $cw[] = $avail * (float)($c[1] ?? 12) / $sum;
    $pages = []; $ops = ''; $y = 0; $pn = 0;
    $rgb = fn($h) => sprintf('%.3f %.3f %.3f', hexdec(substr($h, 0, 2)) / 255, hexdec(substr($h, 2, 2)) / 255, hexdec(substr($h, 4, 2)) / 255);
    $newPage = function () use (&$ops, &$y, &$pn, $PW, $PH, $M, $title, $subtitle, $rgb, $cols, $cw, $fsH, $pad, &$pages) {
        if ($ops !== '') $pages[] = $ops;
        $pn++; $ops = '';
        $ops .= $rgb('2B180E') . " rg 0 " . ($PH - 44) . " $PW 44 re f\n";
        $ops .= "BT /F2 15 Tf " . $rgb('E3B866') . " rg $M " . ($PH - 29) . " Td (" . pdf_esc(pdf_txt(setting('site_name'))) . ") Tj ET\n";
        $ops .= "BT /F1 8 Tf " . $rgb('E8DCC6') . " rg " . ($PW - $M - pdf_w(pdf_txt($title), 8)) . " " . ($PH - 28) . " Td (" . pdf_esc(pdf_txt($title)) . ") Tj ET\n";
        $ops .= "BT /F1 7.4 Tf 0.36 0.30 0.25 rg $M " . ($PH - 60) . " Td (" . pdf_esc(pdf_txt($subtitle)) . ") Tj ET\n";
        $y = $PH - 70; $hh = 17;
        $ops .= $rgb('2B180E') . " rg $M " . ($y - $hh) . " " . ($PW - 2 * $M) . " $hh re f\n"; $x = $M;
        foreach ($cols as $i => $c) {
            $ht = pdf_txt((string)$c[0]); $hw = pdf_w($ht, $fsH, true); $tt = $c[2] ?? 's'; $hx = $x + $pad;
            if ($tt === 'm') $hx = $x + $cw[$i] - $pad - $hw; elseif ($tt === 'c' || $tt === 'n') $hx = $x + ($cw[$i] - $hw) / 2;
            $ops .= "BT /F2 $fsH Tf 1 1 1 rg " . round($hx, 2) . " " . ($y - 11.4) . " Td (" . pdf_esc($ht) . ") Tj ET\n"; $x += $cw[$i];
        }
        $y -= $hh;
    };
    $newPage();
    foreach ($rows as $ri => $r) {
        $cells = []; $maxL = 1;
        foreach ($cols as $i => $c) {
            $v = $r[$i] ?? ''; $t = $c[2] ?? 's';
            if ($t === 'm' && $v !== '' && is_numeric($v)) $v = 'Rs ' . number_format((float)$v);
            $ls = pdf_wrap((string)$v, $cw[$i] - 2 * $pad, $size); $cells[] = $ls; $maxL = max($maxL, count($ls));
        }
        $rh = $maxL * $lead + 2 * $pad - 1;
        if ($y - $rh < $M + 16) $newPage();
        if ($ri % 2) $ops .= $rgb('F6F1E7') . " rg $M " . ($y - $rh) . " " . ($PW - 2 * $M) . " $rh re f\n";
        $ops .= $rgb('E3D9C8') . " RG 0.4 w $M " . ($y - $rh) . " m " . ($PW - $M) . " " . ($y - $rh) . " l S\n";
        $x = $M;
        foreach ($cols as $i => $c) {
            $ty = $y - $pad - $size + 0.6; $t = $c[2] ?? 's';
            foreach ($cells[$i] as $ln) {
                $tx = $x + $pad; $wid = pdf_w($ln, $size);
                if ($t === 'm') $tx = $x + $cw[$i] - $pad - $wid; elseif ($t === 'c' || $t === 'n') $tx = $x + ($cw[$i] - $wid) / 2;
                $ops .= "BT /F1 $size Tf 0.10 0.06 0.04 rg " . round($tx, 2) . " " . round($ty, 2) . " Td (" . pdf_esc($ln) . ") Tj ET\n"; $ty -= $lead;
            }
            $x += $cw[$i];
        }
        $y -= $rh;
    }
    if (!$rows) $ops .= "BT /F1 9 Tf 0.1 0.06 0.04 rg " . ($M + 4) . " " . ($y - 16) . " Td (No records for this selection.) Tj ET\n";
    $pages[] = $ops; $np = count($pages);
    // footers
    foreach ($pages as $i => &$p) $p .= "BT /F1 7 Tf 0.45 0.40 0.35 rg $M 14 Td (" . pdf_esc(pdf_txt(setting('site_name') . ' · ' . rtrim(preg_replace('#^https?://#', '', setting('site_url')), '/'))) . ") Tj ET\n"
        . "BT /F1 7 Tf 0.45 0.40 0.35 rg " . ($PW - $M - 50) . " 14 Td (Page " . ($i + 1) . " of $np) Tj ET\n"; unset($p);
    // assemble objects
    $objs = [1 => '<< /Type /Catalog /Pages 2 0 R >>', 3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>', 4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>'];
    $kids = []; $id = 5;
    foreach ($pages as $p) {
        $stream = function_exists('gzcompress') ? gzcompress($p, 6) : $p; $flt = function_exists('gzcompress') ? ' /Filter /FlateDecode' : '';
        $objs[$id] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 $PW $PH] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents " . ($id + 1) . " 0 R >>";
        $objs[$id + 1] = ['<< /Length ' . strlen($stream) . $flt . ' >>', $stream]; $kids[] = "$id 0 R"; $id += 2;
    }
    $objs[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($kids) . ' >>';
    $objs[$id] = '<< /Title (' . pdf_esc(pdf_txt($title)) . ') /Producer (' . pdf_esc(pdf_txt(setting('site_name'))) . ') /CreationDate (D:' . date('YmdHis') . ') >>'; $infoId = $id;
    ksort($objs); $out = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n"; $off = [];
    foreach ($objs as $n => $o) {
        $off[$n] = strlen($out);
        if (is_array($o)) $out .= "$n 0 obj\n{$o[0]}\nstream\n{$o[1]}\nendstream\nendobj\n"; else $out .= "$n 0 obj\n$o\nendobj\n";
    }
    $xr = strlen($out); $cnt = max(array_keys($objs)) + 1; $out .= "xref\n0 $cnt\n0000000000 65535 f \n";
    for ($n = 1; $n < $cnt; $n++) $out .= sprintf("%010d 00000 n \n", $off[$n]);
    $out .= "trailer\n<< /Size $cnt /Root 1 0 R /Info $infoId 0 R >>\nstartxref\n$xr\n%%EOF";
    header('Content-Type: application/pdf'); header('Content-Disposition: attachment; filename="' . $file . '"'); header('Content-Length: ' . strlen($out)); header('Cache-Control: no-store');
    echo $out; exit;
}
