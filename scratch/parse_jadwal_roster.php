<?php

$file = 'C:/Users/Zulfi/Documents/Data Nurul Furqon/Jadwal Sekolah Pagi.xlsx';

if (!file_exists($file)) {
    die("File not found: " . $file . "\n");
}

// Unzip xlsx file to read XML
$zip = new ZipArchive();
$tempDir = __DIR__ . '/roster_temp/';

if ($zip->open($file) === TRUE) {
    $zip->extractTo($tempDir);
    $zip->close();
    echo "Extracted XLSX to {$tempDir}\n";
} else {
    die("Failed to open XLSX file\n");
}

// Read shared strings
$sharedStrings = [];
if (file_exists($tempDir . 'xl/sharedStrings.xml')) {
    $xml = simplexml_load_file($tempDir . 'xl/sharedStrings.xml');
    foreach ($xml->si as $si) {
        $text = '';
        if (isset($si->t)) {
            $text = (string)$si->t;
        } elseif (isset($si->r)) {
            foreach ($si->r as $r) {
                $text .= (string)$r->t;
            }
        }
        $sharedStrings[] = $text;
    }
}

echo "Shared strings count: " . count($sharedStrings) . "\n";

// Read sheet1.xml
if (file_exists($tempDir . 'xl/worksheets/sheet1.xml')) {
    $sheetXml = simplexml_load_file($tempDir . 'xl/worksheets/sheet1.xml');
    $rowsData = [];

    foreach ($sheetXml->sheetData->row as $row) {
        $rIndex = (int)$row['r'];
        $rowCells = [];

        foreach ($row->c as $c) {
            $r = (string)$c['r'];
            $t = (string)$c['t'];
            $v = (string)$c->v;

            if ($t === 's' && isset($sharedStrings[(int)$v])) {
                $val = $sharedStrings[(int)$v];
            } else {
                $val = $v;
            }

            $rowCells[$r] = $val;
        }

        $rowsData[$rIndex] = $rowCells;
    }

    echo "=== Roster Sheet 1 Layout (First 35 Rows) ===\n";
    foreach ($rowsData as $rIdx => $cells) {
        if ($rIdx > 40) break;
        echo "ROW {$rIdx}: " . implode(' | ', $cells) . "\n";
    }
}
