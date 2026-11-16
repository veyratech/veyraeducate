<?php

declare(strict_types=1);

namespace App\Support;

class CardCodeGenerator
{
    /**
     * Generate pure SVG vector barcode (Code 39 / 128 style representation)
     * Crisp, ultra sharp, scale-independent for print.
     */
    public static function barcodeSvg(string $text, int $width = 140, int $height = 28): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $text) ?: '0081234567';
        $seed = crc32($clean);
        mt_srand($seed);

        $bars = '';
        $x = 4;
        $totalWidth = $width;
        $barHeight = $height - 8;

        // Start guard pattern
        $bars .= "<rect x=\"{$x}\" y=\"2\" width=\"2\" height=\"{$barHeight}\" fill=\"currentColor\" />";
        $x += 4;
        $bars .= "<rect x=\"{$x}\" y=\"2\" width=\"1\" height=\"{$barHeight}\" fill=\"currentColor\" />";
        $x += 3;

        // Generate barcode pattern based on string characters
        $len = strlen($clean);
        for ($i = 0; $i < $len; $i++) {
            $charVal = ord($clean[$i]);
            $widths = [($charVal % 3) + 1, (($charVal >> 2) % 3) + 1, (($charVal >> 4) % 2) + 1];
            foreach ($widths as $w) {
                if ($x + $w + 4 > $totalWidth - 8) {
                    break 2;
                }
                $bars .= "<rect x=\"{$x}\" y=\"2\" width=\"{$w}\" height=\"{$barHeight}\" fill=\"currentColor\" />";
                $x += $w + 2;
            }
        }

        // End guard pattern
        $bars .= "<rect x=\"{$x}\" y=\"2\" width=\"1\" height=\"{$barHeight}\" fill=\"currentColor\" />";
        $x += 3;
        $bars .= "<rect x=\"{$x}\" y=\"2\" width=\"2\" height=\"{$barHeight}\" fill=\"currentColor\" />";

        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$width} {$height}\" width=\"{$width}\" height=\"{$height}\" class=\"inline-block text-slate-800 dark:text-slate-200\">{$bars}<text x=\"50%\" y=\"{$height}\" text-anchor=\"middle\" font-family=\"monospace\" font-size=\"7.5\" font-weight=\"bold\" fill=\"currentColor\">*{$clean}*</text></svg>";
    }

    /**
     * Generate pure SVG vector QR-style matrix with standard locator corner eyes.
     */
    public static function qrCodeSvg(string $text, int $size = 54): string
    {
        $clean = $text ?: 'NISN-0081234567';
        $seed = crc32($clean);
        mt_srand($seed);

        $matrixSize = 21; // standard version 1 QR size
        $cellSize = $size / $matrixSize;
        $rects = '';

        // Helper to add cell
        $addCell = function ($row, $col) use (&$rects, $cellSize) {
            $x = round($col * $cellSize, 2);
            $y = round($row * $cellSize, 2);
            $w = round($cellSize + 0.1, 2);
            $rects .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$w}\" height=\"{$w}\" fill=\"currentColor\" />";
        };

        // Draw 3 standard corner position finder patterns (7x7 eyes)
        $drawFinder = function ($startRow, $startCol) use (&$rects, $cellSize) {
            $x = round($startCol * $cellSize, 2);
            $y = round($startRow * $cellSize, 2);
            $s7 = round(7 * $cellSize, 2);
            $s5 = round(5 * $cellSize, 2);
            $s3 = round(3 * $cellSize, 2);
            $p1 = round(1 * $cellSize, 2);
            $p2 = round(2 * $cellSize, 2);

            // Outer 7x7
            $rects .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$s7}\" height=\"{$s7}\" fill=\"currentColor\" rx=\"2\" />";
            // Inner cutout 5x5
            $xInner = round($x + $p1, 2);
            $yInner = round($y + $p1, 2);
            $rects .= "<rect x=\"{$xInner}\" y=\"{$yInner}\" width=\"{$s5}\" height=\"{$s5}\" fill=\"white\" rx=\"1\" />";
            // Center 3x3
            $xCenter = round($x + $p2, 2);
            $yCenter = round($y + $p2, 2);
            $rects .= "<rect x=\"{$xCenter}\" y=\"{$yCenter}\" width=\"{$s3}\" height=\"{$s3}\" fill=\"currentColor\" rx=\"1\" />";
        };

        $drawFinder(0, 0); // Top-left
        $drawFinder(0, 14); // Top-right
        $drawFinder(14, 0); // Bottom-left

        // Pseudo-random deterministic data payload inside matrix
        for ($r = 0; $r < $matrixSize; $r++) {
            for ($c = 0; $c < $matrixSize; $c++) {
                // Skip finder patterns
                if (($r < 8 && $c < 8) || ($r < 8 && $c > 12) || ($r > 12 && $c < 8)) {
                    continue;
                }
                // Timing patterns
                if ($r === 6 || $c === 6) {
                    if (($r + $c) % 2 === 0) {
                        $addCell($r, $c);
                    }

                    continue;
                }
                // Data modules based on seeded random
                if (mt_rand(0, 100) > 48) {
                    $addCell($r, $c);
                }
            }
        }

        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$size} {$size}\" width=\"{$size}\" height=\"{$size}\" class=\"inline-block text-slate-900 bg-white p-0.5 rounded border border-slate-200/80 shrink-0\">{$rects}</svg>";
    }
}
