<?php

namespace Tests\Feature\PublicPages;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesMarkupTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_publik_memakai_markup_dasar_yang_benar(): void
    {
        foreach (['/', '/login', '/register'] as $url) {
            $response = $this->get($url);

            $response->assertStatus(200);

            $html = $response->getContent();

            $this->assertSame(1, preg_match_all('/<h1\b/i', $html), "Jumlah <h1> tidak tepat satu pada {$url}.");
            $this->assertStringContainsString('<html lang="id"', $html);
            $this->assertStringContainsString('name="viewport"', $html);
            $this->assertImagesHaveDimensionsAndAlt($html, $url);
            $this->assertTidakAdaSimbolTerlarang($html, $url);
        }
    }

    public function test_landing_memiliki_satu_gambar_prioritas_dan_tautan_masuk_daftar(): void
    {
        $response = $this->get('/');
        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, 'fetchpriority="high"'), 'Gambar prioritas tinggi harus tepat satu.');
        $response->assertSee(route('login'), false);
        $response->assertSee(route('register'), false);
        $response->assertSee(route('register', ['tipe' => 'vendor']), false);
    }

    public function test_tautan_tipe_vendor_membuka_pilihan_vendor(): void
    {
        $response = $this->get('/register?tipe=vendor');

        $response->assertStatus(200);

        $this->assertMatchesRegularExpression(
            '/<input[^>]*name="tipe"[^>]*value="vendor"[^>]*checked/',
            $response->getContent()
        );
    }

    private function assertImagesHaveDimensionsAndAlt(string $html, string $url): void
    {
        preg_match_all('/<img\b[^>]*>/i', $html, $matches);

        $this->assertNotEmpty($matches[0], "Tidak ada <img> pada {$url}.");

        foreach ($matches[0] as $image) {
            $this->assertMatchesRegularExpression('/\bwidth="\d+"/', $image, "Atribut width hilang pada {$url}.");
            $this->assertMatchesRegularExpression('/\bheight="\d+"/', $image, "Atribut height hilang pada {$url}.");
            $this->assertMatchesRegularExpression('/\balt="[^"]*"/', $image, "Atribut alt hilang pada {$url}.");
        }
    }

    private function assertTidakAdaSimbolTerlarang(string $html, string $url): void
    {
        $this->assertDoesNotMatchRegularExpression(
            '/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{25A0}-\x{25FF}]/u',
            $html,
            "Ada emoji atau simbol ikon pada {$url}."
        );

        foreach (['indigo-', 'violet-', 'purple-'] as $color) {
            $this->assertStringNotContainsString($color, $html, "Kelas warna terlarang {$color} pada {$url}.");
        }
    }
}
