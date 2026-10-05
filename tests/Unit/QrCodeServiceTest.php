<?php

namespace Tests\Unit;

use App\Services\QrCodeService;
use PHPUnit\Framework\TestCase;

class QrCodeServiceTest extends TestCase
{
    /**
     * Test QR SVG generator menghasilkan format SVG standar yang valid
     */
    public function test_generate_svg_returns_valid_standard_svg(): void
    {
        $payload = 'APAR-RS-001';
        $svg = QrCodeService::generateSvg($payload);

        $this->assertNotEmpty($svg);
        $this->assertStringStartsWith('<svg', $svg);
        $this->assertStringEndsWith('</svg>', trim($svg));
        $this->assertStringNotContainsString('<?xml', $svg);
        $this->assertStringContainsString('viewBox="0 0 200 200"', $svg);
        $this->assertStringContainsString('shape-rendering="crispEdges"', $svg);
    }

    /**
     * Test QR SVG generator mendukung kustomisasi ukuran dan warna
     */
    public function test_generate_svg_with_custom_size_and_colors(): void
    {
        $payload = 'QR-CUSTOM-123';
        $svg = QrCodeService::generateSvg($payload, 300, '#1e293b', '#f8fafc');

        $this->assertNotEmpty($svg);
        $this->assertStringContainsString('viewBox="0 0 300 300"', $svg);
        $this->assertStringContainsString('width="300"', $svg);
        $this->assertStringContainsString('height="300"', $svg);
    }
}
