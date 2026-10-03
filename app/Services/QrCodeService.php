<?php

namespace App\Services;

class QrCodeService
{
    /**
     * Generate standard QR-like SVG vector representation
     * Menghasilkan representasi matriks 2D SVG berstandar visual QR
     * dengan locator kotak 3 sudut (finder patterns), timing patterns,
     * dan data payload deterministik tanpa dependency eksternal.
     */
    public static function generateSvg(string $text, int $size = 200, string $darkColor = '#0f172a', string $lightColor = '#ffffff'): string
    {
        $matrixSize = 25; // 25x25 grid
        $matrix = array_fill(0, $matrixSize, array_fill(0, $matrixSize, false));

        // 1. Draw 3 Finder Patterns (Sudut Kiri Atas, Kanan Atas, Kiri Bawah)
        self::drawFinderPattern($matrix, 0, 0);
        self::drawFinderPattern($matrix, 0, $matrixSize - 7);
        self::drawFinderPattern($matrix, $matrixSize - 7, 0);

        // 2. Draw Timing Patterns (garis putus-putus penghubung)
        for ($i = 8; $i < $matrixSize - 8; $i++) {
            $matrix[6][$i] = ($i % 2 === 0);
            $matrix[$i][6] = ($i % 2 === 0);
        }

        // 3. Draw Deterministic Data Modules from Payload
        $hash = hash('sha256', $text);
        $hashLen = strlen($hash);
        $bitIndex = 0;

        for ($r = 0; $r < $matrixSize; $r++) {
            for ($c = 0; $c < $matrixSize; $c++) {
                // Jangan timpa finder pattern dan timing patterns
                if (self::isReserved($r, $c, $matrixSize)) {
                    continue;
                }

                $char = $hash[$bitIndex % $hashLen];
                $val = hexdec($char);
                // Set module bit based on payload hash & position
                $matrix[$r][$c] = (($val + $r * 3 + $c * 7) % 3 === 0);
                $bitIndex++;
            }
        }

        // 4. Render to crisp SVG
        $moduleSize = 8;
        $svgDim = $matrixSize * $moduleSize;
        $padding = 16;
        $totalSize = $svgDim + ($padding * 2);

        $rects = [];
        for ($r = 0; $r < $matrixSize; $r++) {
            for ($c = 0; $c < $matrixSize; $c++) {
                if ($matrix[$r][$c]) {
                    $x = $padding + ($c * $moduleSize);
                    $y = $padding + ($r * $moduleSize);
                    $rects[] = sprintf('<rect x="%d" y="%d" width="%d" height="%d" fill="%s" />', $x, $y, $moduleSize, $moduleSize, $darkColor);
                }
            }
        }

        $rectsSvg = implode("\n    ", $rects);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$totalSize} {$totalSize}" width="{$size}" height="{$size}" shape-rendering="crispEdges">
    <rect width="100%" height="100%" fill="{$lightColor}" />
    {$rectsSvg}
</svg>
SVG;
    }

    private static function drawFinderPattern(array &$matrix, int $startRow, int $startCol): void
    {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                $isBorder = ($r === 0 || $r === 6 || $c === 0 || $c === 6);
                $isCenter = ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4);
                $matrix[$startRow + $r][$startCol + $c] = ($isBorder || $isCenter);
            }
        }
    }

    private static function isReserved(int $r, int $c, int $size): bool
    {
        // Top-left finder + separator
        if ($r <= 7 && $c <= 7) return true;
        // Top-right finder + separator
        if ($r <= 7 && $c >= $size - 8) return true;
        // Bottom-left finder + separator
        if ($r >= $size - 8 && $c <= 7) return true;
        // Timing patterns
        if ($r === 6 || $c === 6) return true;

        return false;
    }
}
