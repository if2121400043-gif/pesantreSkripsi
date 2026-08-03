<?php

// Target totals from Hasil Penelitian.docx:
// SS = 56
// S  = 37
// N  = 7
// TS = 0
// STS= 0
// Total = 100 answers (20 respondents * 5 questions)

$respondents = [
    ["1", "Saiq Khairan", "Admin"],
    ["2", "Moh. Danil", "Guru"],
    ["3", "Ach. Baskara", "Guru"],
    ["4", "Ferdiyansyah", "Admin"],
    ["5", "Riko Saputra", "Guru"],
    ["6", "Suparman", "Admin"],
    ["7", "Karina Purnomo W.", "Guru"],
    ["8", "Moh. Amin Nurul J.", "Wali S"],
    ["9", "Firman W.", "Wali S"],
    ["10", "Eva Suryana", "Wali S"],
    ["11", "Sofiatul Nur Aini", "Wali S"],
    ["12", "Afifuddin", "Wali S"],
    ["13", "Ahmad Rifa’i", "Wali S"],
    ["14", "Abdur Rahmad", "Wali S"],
    ["15", "Irzem", "Guru"],
    ["16", "Sumawiya", "Guru"],
    ["17", "Sini Nur Hasanah", "Guru"],
    ["18", "Riki Habibi", "Admin"],
    ["19", "Moh. Jasuli", "Admin"],
    ["20", "Titik Bawon", "Admin"],
];

// Proposed 20-row distribution for Skala Likert Hasil.docx [SS, S, N, TS, STS] (each row sums to 5):
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

$sumSS = 0; $sumS = 0; $sumN = 0; $sumTS = 0; $sumSTS = 0;
foreach ($distribution as $row) {
    $sumSS += $row[0];
    $sumS  += $row[1];
    $sumN  += $row[2];
    $sumTS += $row[3];
    $sumSTS+= $row[4];
}

echo "=== VERIFIKASI DISTRIBUSI BARU ===\n";
echo "Total SS (5) : $sumSS (Target: 56)\n";
echo "Total S  (4) : $sumS  (Target: 37)\n";
echo "Total N  (3) : $sumN  (Target: 7)\n";
echo "Total TS (2) : $sumTS (Target: 0)\n";
echo "Total STS(1) : $sumSTS(Target: 0)\n";
echo "Total Skor   : " . (($sumSS*5) + ($sumS*4) + ($sumN*3)) . " / 500 = " . ((($sumSS*5) + ($sumS*4) + ($sumN*3))/5) . "%\n";

if ($sumSS === 56 && $sumS === 37 && $sumN === 7) {
    echo "✅ PERFECT! SUM MATCHES TARGET 100%!\n";
} else {
    echo "❌ DOES NOT MATCH!\n";
}
