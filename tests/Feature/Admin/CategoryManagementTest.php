<?php

namespace Tests\Feature\Admin;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Event;
use App\Models\Role;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_dapat_melihat_daftar_kategori(): void
    {
        Category::factory()->create([
            'nama_kategori' => 'Pernikahan Tradisional',
            'ikon' => 'heart',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/kategori');

        $response->assertStatus(200);
        $response->assertSee('Pernikahan Tradisional');
    }

    public function test_admin_dapat_menambah_kategori_baru(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/kategori', [
            'nama_kategori' => 'Konser Musik',
            'ikon' => 'mic',
            'deskripsi' => 'Acara panggung hiburan musik live',
        ]);

        $response->assertRedirect('/admin/kategori');
        $this->assertDatabaseHas('categories', [
            'nama_kategori' => 'Konser Musik',
            'ikon' => 'mic',
        ]);
    }

    public function test_nama_kategori_wajib_dan_harus_unik(): void
    {
        Category::factory()->create(['nama_kategori' => 'Seminar']);

        $response = $this->actingAs($this->admin)->post('/admin/kategori', [
            'nama_kategori' => 'Seminar',
        ]);

        $response->assertSessionHasErrors('nama_kategori');
    }

    public function test_admin_dapat_mengubah_kategori(): void
    {
        $category = Category::factory()->create([
            'nama_kategori' => 'Workshop Lama',
        ]);

        $response = $this->actingAs($this->admin)->put('/admin/kategori/'.$category->id, [
            'nama_kategori' => 'Workshop Baru',
            'ikon' => 'heart',
            'deskripsi' => 'Pelatihan intensif',
        ]);

        $response->assertRedirect('/admin/kategori');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'nama_kategori' => 'Workshop Baru',
            'ikon' => 'heart',
        ]);
    }

    public function test_hapus_kategori_ditolak_jika_sedang_digunakan_template_d23(): void
    {
        $category = Category::factory()->create();
        Template::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->admin)->delete('/admin/kategori/'.$category->id);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_hapus_kategori_ditolak_jika_sedang_digunakan_acara_d23(): void
    {
        $category = Category::factory()->create();
        Event::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->admin)->delete('/admin/kategori/'.$category->id);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_hapus_kategori_sukses_jika_tidak_digunakan(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->delete('/admin/kategori/'.$category->id);

        $response->assertRedirect('/admin/kategori');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
