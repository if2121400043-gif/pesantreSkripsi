<?php

$rowConstraints = [
    [3, 1, 1], // 1. Saiq Khairan
    [2, 3, 0], // 2. Moh. Danil
    [3, 2, 0], // 3. Ach. Baskara
    [2, 3, 0], // 4. Ferdiyansyah
    [4, 1, 0], // 5. Riko Saputra
    [2, 2, 1], // 6. Suparman
    [2, 2, 1], // 7. Karina Purnomo W.
    [4, 1, 0], // 8. Moh. Amin Nurul J.
    [4, 1, 0], // 9. Firman W.
    [3, 1, 1], // 10. Eva Suryana
    [2, 2, 1], // 11. Sofiatul Nur Aini
    [4, 1, 0], // 12. Afifuddin
    [2, 2, 1], // 13. Ahmad Rifa’i
    [4, 1, 0], // 14. Abdur Rahmad
    [2, 3, 0], // 15. Irzem
    [3, 2, 0], // 16. Sumawiya
    [4, 1, 0], // 17. Sini Nur Hasanah
    [3, 2, 0], // 18. Riki Habibi
    [2, 3, 0], // 19. Moh. Jasuli
    [1, 3, 1], // 20. Titik Bawon
];

$targetColSS = [11, 10, 12, 10, 13];
$targetColS  = [8,  8,  7,  9,  5];
$targetColN  = [1,  2,  1,  1,  2];

function permuteArray($arr, $prefix, &$perms) {
    if (count($arr) == 0) {
        $perms[implode(',', $prefix)] = $prefix;
        return;
    }
    for ($i = 0; $i < count($arr); $i++) {
        $rem = $arr;
        array_splice($rem, $i, 1);
        permuteArray($rem, array_merge($prefix, [$arr[$i]]), $perms);
    }
}

// Generate row permutations
$rowOptions = [];
foreach ($rowConstraints as $rIdx => $c) {
    $ss = $c[0]; $s = $c[1]; $n = $c[2];
    $items = array_merge(array_fill(0, $ss, 5), array_fill(0, $s, 4), array_fill(0, $n, 3));
    
    $perms = [];
    permuteArray($items, [], $perms);
    $rowOptions[$rIdx] = array_values($perms);
}

// Backtracking solver
$curMatrix = [];
$foundMatrix = null;

function solveRow($rIdx, $curSS, $curS, $curN) {
    global $rowOptions, $targetColSS, $targetColS, $targetColN, $curMatrix, $foundMatrix;

    if ($foundMatrix) return;

    if ($rIdx == 20) {
        if ($curSS == $targetColSS && $curS == $targetColS && $curN == $targetColN) {
            $foundMatrix = $curMatrix;
        }
        return;
    }

    // Pruning check
    for ($c = 0; $c < 5; $c++) {
        if ($curSS[$c] > $targetColSS[$c] || $curS[$c] > $targetColS[$c] || $curN[$c] > $targetColN[$c]) {
            return;
        }
    }

    foreach ($rowOptions[$rIdx] as $option) {
        $nextSS = $curSS;
        $nextS  = $curS;
        $nextN  = $curN;

        for ($c = 0; $c < 5; $c++) {
            $val = $option[$c];
            if ($val == 5) $nextSS[$c]++;
            if ($val == 4) $nextS[$c]++;
            if ($val == 3) $nextN[$c]++;
        }

        $curMatrix[$rIdx] = $option;
        solveRow($rIdx + 1, $nextSS, $nextS, $nextN);
        if ($foundMatrix) return;
    }
}

solveRow(0, [0,0,0,0,0], [0,0,0,0,0], [0,0,0,0,0]);

if ($foundMatrix) {
    echo "=== EXACT SOLVED MATRIX FOUND ===\n";
    echo var_export($foundMatrix, true) . ";\n";
} else {
    echo "No exact combination found.\n";
}
