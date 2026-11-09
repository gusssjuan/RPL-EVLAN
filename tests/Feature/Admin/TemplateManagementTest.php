<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Role;
use App\Models\Template;
use App\Models\TemplateItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->admin = User::factory()->admin()->create();
        $this->category = Category::factory()->create(['nama_kategori' => 'Seminar']);
    }

    public function test_admin_dapat_melihat_daftar_template(): void
    {
        Template::factory()->create([
            'category_id' => $this->category->id,
            'nama_template' => 'Template Seminar Kampus',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/template');

        $response->assertStatus(200);
        $response->assertSee('Template Seminar Kampus');
    }

    public function test_admin_dapat_membuat_template_baru_beserta_empat_komponen(): void
    {
        $payload = [
            'nama_template' => 'Template Seminar Lengkap',
            'category_id' => $this->category->id,
            'is_aktif' => '1',
            'kebutuhan' => [
                ['nama_item' => 'Sound system 4 mic', 'kelompok' => 'Perlengkapan'],
                ['nama_item' => 'Snack box 300 porsi', 'kelompok' => 'Konsumsi'],
            ],
            'timeline' => [
                ['nama_item' => 'Booking venue', 'offset_hari' => '60'],
                ['nama_item' => 'Gladi bersih', 'offset_hari' => '1'],
            ],
            'divisi' => [
                ['nama_item' => 'Acara'],
                ['nama_item' => 'Konsumsi'],
            ],
            'jobdesk' => [
                ['nama_item' => 'Menyusun rundown', 'kelompok' => 'Acara'],
                ['nama_item' => 'Survei katering', 'kelompok' => 'Konsumsi'],
            ],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/template', $payload);

        $response->assertRedirect('/admin/template');

        $this->assertDatabaseHas('templates', [
            'nama_template' => 'Template Seminar Lengkap',
            'category_id' => $this->category->id,
            'is_aktif' => true,
        ]);

        $template = Template::where('nama_template', 'Template Seminar Lengkap')->first();
        $this->assertCount(6, $template->items);
        $this->assertEquals(2, $template->kebutuhan()->count());
        $this->assertEquals(2, $template->timeline()->count());
        $this->assertEquals(2, $template->divisi()->count());
        $this->assertEquals(2, $template->jobdesk()->count());
    }

    public function test_admin_dapat_menduplikasi_template(): void
    {
        $template = Template::factory()->create([
            'category_id' => $this->category->id,
            'nama_template' => 'Template Asli',
        ]);

        TemplateItem::factory()->create([
            'template_id' => $template->id,
            'jenis' => 'kebutuhan',
            'nama_item' => 'Proyektor HD',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/template/'.$template->id.'/duplikat');

        $response->assertRedirect('/admin/template');

        $this->assertDatabaseHas('templates', [
            'nama_template' => 'Salinan - Template Asli',
            'is_aktif' => false,
        ]);

        $duplikat = Template::where('nama_template', 'Salinan - Template Asli')->first();
        $this->assertCount(1, $duplikat->items);
        $this->assertEquals('Proyektor HD', $duplikat->items->first()->nama_item);
    }

    public function test_mengaktifkan_template_menonaktifkan_template_lain_pada_kategori_sama_d07(): void
    {
        $templateLama = Template::factory()->create([
            'category_id' => $this->category->id,
            'nama_template' => 'Template Lama Aktif',
            'is_aktif' => true,
        ]);

        $templateBaru = Template::factory()->create([
            'category_id' => $this->category->id,
            'nama_template' => 'Template Baru Mau Diaktifkan',
            'is_aktif' => false,
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/template/'.$templateBaru->id.'/aktifkan');

        $response->assertRedirect('/admin/template');

        $this->assertTrue($templateBaru->fresh()->is_aktif);
        $this->assertFalse($templateLama->fresh()->is_aktif);
    }
}

