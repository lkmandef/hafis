<?php

namespace App\Services;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    /**
     * Generate standard QR Code SVG vector representation
     * Menghasilkan representasi SVG QR Code standar ISO/IEC 18004 yang valid
     * dan dapat dibaca oleh scanner kamera serta decoder QR standar.
     */
    public static function generateSvg(string $text, int $size = 200, string $darkColor = '#0f172a', string $lightColor = '#ffffff'): string
    {
        $foreColor = self::parseColor($darkColor, new Rgb(15, 23, 42));
        $backColor = self::parseColor($lightColor, new Rgb(255, 255, 255));

        $fill = Fill::uniformColor($backColor, $foreColor);
        $rendererStyle = new RendererStyle($size, 2, null, null, $fill);
        $renderer = new ImageRenderer($rendererStyle, new SvgImageBackEnd);
        $writer = new Writer($renderer);

        $svg = $writer->writeString($text, 'UTF-8', ErrorCorrectionLevel::M());

        // Strip XML declaration agar dapat di-render langsung secara inline di template Blade
        $svg = preg_replace('/<\?xml[^>]*\?>\s*/i', '', $svg) ?? $svg;

        // Tambahkan atribut styling agar tajam dan responsif di dalam container
        return str_replace('<svg ', '<svg class="w-full h-full max-w-full max-h-full" shape-rendering="crispEdges" ', $svg);
    }

    /**
     * Parse hex color string ke objek ColorInterface BaconQrCode
     */
    private static function parseColor(string $color, Rgb $default): Rgb|Alpha
    {
        $color = trim($color);

        if (strtolower($color) === 'transparent') {
            return new Alpha(0, new Rgb(255, 255, 255));
        }

        $hex = ltrim($color, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return $default;
        }

        $r = (int) hexdec(substr($hex, 0, 2));
        $g = (int) hexdec(substr($hex, 2, 2));
        $b = (int) hexdec(substr($hex, 4, 2));

        return new Rgb($r, $g, $b);
    }
}
