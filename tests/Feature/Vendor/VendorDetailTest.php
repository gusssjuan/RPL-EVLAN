<?php

namespace Tests\Feature\Vendor;

use App\Enums\RoleName;
use App\Enums\StatusVerifikasi;
use App\Models\Category;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $member;

    protected Vendor $vendor;

    protected VendorProduct $product;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);

        $this->leader = User::factory()->create(['nama' => 'Gus Ega']);
        $this->member = User::factory()->create(['nama' => 'Rina Astuti']);

        $category = Category::factory()->create(['nama_kategori' => 'Seminar']);

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $category->id,
            'nama_event' => 'Seminar Teknologi 2026',
        ]);

        $this->vendor = Vendor::factory()->terverifikasi()->create([
            'nama_vendor' => 'Sinar Sound System',
            'kontak' => '081299990001',
            'kota' => 'Denpasar',
        ]);

        $this->product = VendorProduct::factory()->create([
            'vendor_id' => $this->vendor->id,
            'nama_produk' => 'Paket Sound System Seminar',
            'harga' => 3500000,
        ]);
    }

    public function test_detail_vendor_menampilkan_produk_dan_kontak(): void
    {
        $response = $this->actingAs($this->member)->get('/vendor/'.$this->vendor->id);

        $response->assertStatus(200);
        $response->assertSee('Sinar Sound System');
        $response->assertSee('Paket Sound System Seminar');
        $response->assertSee('081299990001');
        $response->assertDontSee('Tambahkan ke Anggaran');
    }

    public function test_leader_dapat_menambahkan_produk_vendor_ke_anggaran(): void
    {
        $response = $this->actingAs($this->leader)->post('/vendor/'.$this->vendor->id.'/anggaran', [
            'event_id' => $this->event->id,
            'vendor_product_id' => $this->product->id,
            'jumlah' => 2,
        ]);

        $response->assertRedirect('/acara/'.$this->event->id.'/anggaran');

        $this->assertDatabaseHas('budgets', [
            'event_id' => $this->event->id,
            'vendor_product_id' => $this->product->id,
            'nama_item' => 'Paket Sound System Seminar',
            'jumlah' => 2,
            'harga_satuan' => 3500000,
            'total' => 7000000,
        ]);
    }

    public function test_non_leader_tidak_dapat_menambahkan_ke_anggaran(): void
    {
        $response = $this->actingAs($this->member)->post('/vendor/'.$this->vendor->id.'/anggaran', [
            'event_id' => $this->event->id,
            'vendor_product_id' => $this->product->id,
            'jumlah' => 1,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('budgets', 0);
    }

    public function test_vendor_belum_terverifikasi_tidak_dapat_dilihat(): void
    {
        $this->vendor->update(['status_verifikasi' => StatusVerifikasi::Menunggu]);

        $this->actingAs($this->member)
            ->get('/vendor/'.$this->vendor->id)
            ->assertStatus(404);
    }
}
