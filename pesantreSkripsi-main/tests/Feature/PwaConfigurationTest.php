<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaConfigurationTest extends TestCase
{
    public function test_manifest_contains_android_friendly_pwa_settings(): void
    {
        $response = $this->get('/manifest.json');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/manifest+json; charset=utf-8');

        $content = file_get_contents(public_path('manifest.json'));
        $this->assertStringContainsString('"display": "standalone"', $content);
        $this->assertStringContainsString('"start_url": "/?source=pwa"', $content);
        $data = json_decode($content, true);

        $this->assertIsArray($data);
        $this->assertSame('Sistem Informasi Manajemen Pondok Pesantren Nurul Furqon', $data['name']);
        $this->assertSame('PP Nurul Furqon', $data['short_name']);
        $this->assertSame('standalone', $data['display']);
        $this->assertSame('portrait-primary', $data['orientation']);
        $this->assertSame('id-ID', $data['lang']);
        $this->assertSame('/', $data['id']);
        $this->assertSame('/?source=pwa', $data['start_url']);
        $this->assertSame('/', $data['scope']);
        $this->assertContains('standalone', $data['display_override']);
        $this->assertSame('#065f46', $data['theme_color']);
        $this->assertSame('#065f46', $data['background_color']);

        $icon192 = collect($data['icons'])->firstWhere('sizes', '192x192');
        $this->assertNotNull($icon192);
        $this->assertStringContainsString('maskable', $icon192['purpose']);
    }
}
