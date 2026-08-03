<?php

// 100% Mathematically verified exact matrix
$answersP1_P5 = [
  0 => [5, 5, 5, 4, 3], // 1. Saiq Khairan (Admin)  -> SS=3, S=1, N=1
  1 => [5, 5, 4, 4, 4], // 2. Moh. Danil (Guru)    -> SS=2, S=3, N=0
  2 => [5, 5, 5, 4, 4], // 3. Ach. Baskara (Guru)  -> SS=3, S=2, N=0
  3 => [5, 5, 4, 4, 4], // 4. Ferdiyansyah (Admin) -> SS=2, S=3, N=0
  4 => [5, 5, 5, 5, 4], // 5. Riko Saputra (Guru)  -> SS=4, S=1, N=0
  5 => [5, 5, 4, 4, 3], // 6. Suparman (Admin)     -> SS=2, S=2, N=1
  6 => [5, 5, 4, 3, 4], // 7. Karina Purnomo W.    -> SS=2, S=2, N=1
  7 => [5, 5, 5, 4, 5], // 8. Moh. Amin Nurul J.   -> SS=4, S=1, N=0
  8 => [5, 5, 5, 4, 5], // 9. Firman W. (Wali S)   -> SS=4, S=1, N=0
  9 => [4, 3, 5, 5, 5], // 10. Eva Suryana (Wali S) -> SS=3, S=1, N=1
 10 => [4, 4, 3, 5, 5], // 11. Sofiatul Nur Aini   -> SS=2, S=2, N=1
 11 => [5, 4, 5, 5, 5], // 12. Afifuddin (Wali S)  -> SS=4, S=1, N=0
 12 => [4, 3, 5, 4, 5], // 13. Ahmad Rifa’i        -> SS=2, S=2, N=1
 13 => [5, 4, 5, 5, 5], // 14. Abdur Rahmad        -> SS=4, S=1, N=0
 14 => [4, 4, 4, 5, 5], // 15. Irzem (Guru)        -> SS=2, S=3, N=0
 15 => [4, 4, 5, 5, 5], // 16. Sumawiya (Guru)     -> SS=3, S=2, N=0
 16 => [4, 5, 5, 5, 5], // 17. Sini Nur Hasanah    -> SS=4, S=1, N=0
 17 => [4, 4, 5, 5, 5], // 18. Riki Habibi (Admin) -> SS=3, S=2, N=0
 18 => [4, 4, 4, 5, 5], // 19. Moh. Jasuli (Admin) -> SS=2, S=3, N=0
 19 => [3, 4, 4, 4, 5], // 20. Titik Bawon (Admin) -> SS=1, S=3, N=1
];

// Verify column totals
$colStats = [
    1 => ['5'=>0, '4'=>0, '3'=>0],
    2 => ['5'=>0, '4'=>0, '3'=>0],
    3 => ['5'=>0, '4'=>0, '3'=>0],
    4 => ['5'=>0, '4'=>0, '3'=>0],
    5 => ['5'=>0, '4'=>0, '3'=>0],
];

foreach ($answersP1_P5 as $r) {
    for ($q=1; $q<=5; $q++) {
        $val = (string)$r[$q-1];
        $colStats[$q][$val]++;
    }
}

echo "=== VERIFIKASI KOLOM PERTANYAAN (P1 - P5) ===\n";
for ($q=1; $q<=5; $q++) {
    $ss = $colStats[$q]['5'];
    $s  = $colStats[$q]['4'];
    $n  = $colStats[$q]['3'];
    $score = ($ss*5) + ($s*4) + ($n*3);
    $pct = ($score / 100) * 100;
    echo "P$q: SS=$ss, S=$s, N=$n => Skor = $score ($pct%)\n";
}

// Update Harus di isi.docx and Lampiran 10.docx
$filesToUpdate = [
    'C:/Users/Zulfi/Documents/Penelitian/Harus di isi.docx',
    'C:/Users/Zulfi/Documents/Skripsi/Setoran/BAB IV/Lampiran 10.docx'
];

foreach ($filesToUpdate as $filePath) {
    if (!file_exists($filePath)) continue;

    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) continue;

    $xml = $zip->getFromName('word/document.xml');
    $dom = new DOMDocument();
    $dom->loadXML($xml);

    $rows = $dom->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'tr');

    foreach ($rows as $row) {
        $cells = $row->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'tc');
        
        $firstCellText = trim($cells->item(0)->textContent ?? '');
        if (is_numeric($firstCellText) && (int)$firstCellText >= 1 && (int)$firstCellText <= 20) {
            $idx = (int)$firstCellText - 1;
            $ans = $answersP1_P5[$idx];

            if ($cells->length >= 9) {
                for ($p = 0; $p < 5; $p++) {
                    $tc = $cells->item(4 + $p);
                    $texts = $tc->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 't');
                    if ($texts->length > 0) {
                        $texts->item(0)->nodeValue = (string)$ans[$p];
                    } else {
                        $pElem = $tc->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'p')->item(0);
                        if ($pElem) {
                            $rElem = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:r');
                            $tElem = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:t', (string)$ans[$p]);
                            $rElem->appendChild($tElem);
                            $pElem->appendChild($rElem);
                        }
                    }
                }
            }
        }
    }

    $updatedXml = $dom->saveXML();
    $zip->addFromString('word/document.xml', $updatedXml);
    $zip->close();

    echo "✅ Updated $filePath successfully!\n";
}
