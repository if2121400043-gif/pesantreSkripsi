<?php

$file1 = 'C:/Users/Zulfi/Documents/Skripsi/Setoran/BAB IV/Skala Likert Hasil.docx';

$distribution = [
    [3, 1, 1, 0, 0], // 1. Saiq Khairan (Admin)
    [2, 3, 0, 0, 0], // 2. Moh. Danil (Guru)
    [3, 2, 0, 0, 0], // 3. Ach. Baskara (Guru)
    [2, 3, 0, 0, 0], // 4. Ferdiyansyah (Admin)
    [4, 1, 0, 0, 0], // 5. Riko Saputra (Guru)
    [2, 2, 1, 0, 0], // 6. Suparman (Admin)
    [2, 2, 1, 0, 0], // 7. Karina Purnomo W. (Guru)
    [4, 1, 0, 0, 0], // 8. Moh. Amin Nurul J. (Wali S)
    [4, 1, 0, 0, 0], // 9. Firman W. (Wali S)
    [3, 1, 1, 0, 0], // 10. Eva Suryana (Wali S)
    [2, 2, 1, 0, 0], // 11. Sofiatul Nur Aini (Wali S)
    [4, 1, 0, 0, 0], // 12. Afifuddin (Wali S)
    [2, 2, 1, 0, 0], // 13. Ahmad Rifa’i (Wali S)
    [4, 1, 0, 0, 0], // 14. Abdur Rahmad (Wali S)
    [2, 3, 0, 0, 0], // 15. Irzem (Guru)
    [3, 2, 0, 0, 0], // 16. Sumawiya (Guru)
    [4, 1, 0, 0, 0], // 17. Sini Nur Hasanah (Guru)
    [3, 2, 0, 0, 0], // 18. Riki Habibi (Admin)
    [2, 3, 0, 0, 0], // 19. Moh. Jasuli (Admin)
    [1, 3, 1, 0, 0], // 20. Titik Bawon (Admin)
];

$zip = new ZipArchive();
if ($zip->open($file1) !== true) {
    die("Cannot open file1\n");
}

$xml = $zip->getFromName('word/document.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);

$rows = $dom->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'tr');

foreach ($rows as $i => $row) {
    if ($i == 0) continue; // Header row
    $distIndex = $i - 1;
    if (!isset($distribution[$distIndex])) continue;

    $cells = $row->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'tc');
    
    // Indices 3, 4, 5, 6, 7 are SS, S, N, TS, STS
    $vals = $distribution[$distIndex];
    for ($col = 0; $col < 5; $col++) {
        $cellIndex = $col + 3;
        if ($cells->item($cellIndex)) {
            $tc = $cells->item($cellIndex);
            $texts = $tc->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 't');
            if ($texts->length > 0) {
                $texts->item(0)->nodeValue = (string)$vals[$col];
            } else {
                $p = $tc->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'p')->item(0);
                if ($p) {
                    $r = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:r');
                    $t = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:t', (string)$vals[$col]);
                    $r->appendChild($t);
                    $p->appendChild($r);
                }
            }
        }
    }
}

$updatedXml = $dom->saveXML();
$zip->addFromString('word/document.xml', $updatedXml);
$zip->close();

echo "Updated $file1 successfully!\n";
