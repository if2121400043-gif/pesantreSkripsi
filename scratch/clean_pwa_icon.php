<?php

ini_set('memory_limit', '512M');

$srcPath = __DIR__ . '/../public/images/logo-pesantren-256.webp';
if (!file_exists($srcPath)) {
    $srcPath = __DIR__ . '/../public/images/logo-pesantren.webp';
}

if (!file_exists($srcPath)) {
    die("Logo lembaga not found\n");
}

echo "Using logo source: {$srcPath}\n";

$img = imagecreatefromwebp($srcPath);
if (!$img) {
    die("Failed to load webp logo\n");
}

$w = imagesx($img);
$h = imagesy($img);
echo "Logo dimensions: {$w}x{$h}\n";

// Function to generate clean PNG icon without white background border
function generateCleanIcon($srcImg, $srcW, $srcH, $targetSize, $outputPath, $isMaskable = false) {
    $canvas = imagecreatetruecolor($targetSize, $targetSize);
    
    // Enable alpha transparency
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);

    if ($isMaskable) {
        // Maskable icon uses Emerald 800 background (#065f46) to fill edge-to-edge
        $bg = imagecolorallocate($canvas, 6, 95, 70);
        imagefilledrectangle($canvas, 0, 0, $targetSize, $targetSize, $bg);
        $innerSize = (int)($targetSize * 0.85); // 85% safe zone
        $offset = (int)(($targetSize - $innerSize) / 2);
    } else {
        // Standard PWA icon & Apple touch icon use transparent background with full logo fit
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $targetSize, $targetSize, $transparent);
        $innerSize = $targetSize;
        $offset = 0;
    }

    imagealphablending($canvas, true);
    imagecopyresampled($canvas, $srcImg, $offset, $offset, 0, 0, $innerSize, $innerSize, $srcW, $srcH);

    // If non-maskable, make any remaining pure white background transparent
    if (!$isMaskable) {
        for ($x = 0; $x < $targetSize; $x++) {
            for ($y = 0; $y < $targetSize; $y++) {
                $rgb = imagecolorat($canvas, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $a = ($rgb >> 24) & 0x7F;

                // Strip pure white background pixels
                if ($r >= 245 && $g >= 245 && $b >= 245) {
                    $transColor = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
                    imagesetpixel($canvas, $x, $y, $transColor);
                }
            }
        }
    }

    imagepng($canvas, $outputPath);
    imagedestroy($canvas);
}

$iconsDir = __DIR__ . '/../public/icons/';
if (!file_exists($iconsDir)) {
    mkdir($iconsDir, 0755, true);
}

generateCleanIcon($img, $w, $h, 512, $iconsDir . 'icon-512x512.png', false);
generateCleanIcon($img, $w, $h, 192, $iconsDir . 'icon-192x192.png', false);
generateCleanIcon($img, $w, $h, 180, $iconsDir . 'apple-touch-icon.png', false);
generateCleanIcon($img, $w, $h, 192, $iconsDir . 'icon-192x192-maskable.png', true);
generateCleanIcon($img, $w, $h, 512, $iconsDir . 'icon-512x512-maskable.png', true);

// Also copy as favicon.ico
generateCleanIcon($img, $w, $h, 64, __DIR__ . '/../public/favicon.ico', false);

imagedestroy($img);
echo "✅ PWA Icons successfully generated directly from logo-pesantren-256.webp without white border!\n";
