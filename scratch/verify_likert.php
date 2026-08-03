<?php

$file1 = 'C:/Users/Zulfi/Documents/Skripsi/Setoran/BAB IV/Skala Likert Hasil.docx';

$zip = new ZipArchive();
if ($zip->open($file1) !== true) {
    die("Cannot open docx\n");
}
$xml = $zip->getFromName('word/document.xml');
$zip->close();

$dom = new DOMDocument();
$dom->loadXML($xml);

$rows = $dom->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'tr');

echo "Total table rows: " . $rows->length . "\n";

$data = [];
$totalSS = 0;
$totalS = 0;
$totalN = 0;
$totalTS = 0;
$totalSTS = 0;

foreach ($rows as $i => $row) {
    $cells = $row->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'tc');
    $rowText = [];
    foreach ($cells as $cell) {
        $rowText[] = trim($cell->textContent);
    }
    
    echo "Row $i: " . implode(' | ', $rowText) . "\n";

    if ($i > 0 && count($rowText) >= 8) {
        $ss = (int)($rowText[3] ?? 0);
        $s  = (int)($rowText[4] ?? 0);
        $n  = (int)($rowText[5] ?? 0);
        $ts = (int)($rowText[6] ?? 0);
        $sts= (int)($rowText[7] ?? 0);

        $totalSS += $ss;
        $totalS  += $s;
        $totalN  += $n;
        $totalTS += $ts;
        $totalSTS+= $sts;
    }
}

echo "\n========================================\n";
echo "REKAPITULASI DARI Skala Likert Hasil.docx:\n";
echo "Total SS (5) : $totalSS (Target Hasil Penelitian: 56? Let's check target totals)\n";
echo "Total S  (4) : $totalS\n";
echo "Total N  (3) : $totalN\n";
echo "Total TS (2) : $totalTS\n";
echo "Total STS(1) : $totalSTS\n";
echo "Total Pertanyaan Keseluruhan (20 Responden x 5 P1-P5) = " . ($totalSS + $totalS + $totalN + $totalTS + $totalSTS) . "\n";
