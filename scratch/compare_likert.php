<?php

// File 1: Skala Likert Hasil.docx
$file1 = 'C:/Users/Zulfi/Documents/Skripsi/Setoran/BAB IV/Skala Likert Hasil.docx';
// File 2: Hasil Penelitian.docx
$file2 = 'C:/Users/Zulfi/Documents/Penelitian/Hasil Penelitian.docx';

function extractDocxText($filePath) {
    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        return ["ERROR: Cannot open $filePath"];
    }
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();

    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $paragraphs = $dom->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'p');
    $lines = [];
    foreach ($paragraphs as $p) {
        $texts = $p->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 't');
        $line = '';
        foreach ($texts as $t) {
            $line .= $t->textContent;
        }
        $lines[] = $line;
    }
    return $lines;
}

echo "========================================\n";
echo "FILE 1: Skala Likert Hasil.docx\n";
echo "========================================\n";
$lines1 = extractDocxText($file1);
foreach ($lines1 as $i => $l) {
    if (trim($l) !== '') {
        echo "L" . ($i+1) . ": " . $l . "\n";
    }
}

echo "\n========================================\n";
echo "FILE 2: Hasil Penelitian.docx\n";
echo "========================================\n";
$lines2 = extractDocxText($file2);
foreach ($lines2 as $i => $l) {
    if (trim($l) !== '') {
        echo "L" . ($i+1) . ": " . $l . "\n";
    }
}
