<?php

namespace Tests\Feature\Event;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $member;

    protected User $outsider;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);

        $this->leader = User::factory()->create(['nama' => 'Gus Ega Leader']);
        $this->member = User::factory()->create(['nama' => 'Rina Member']);
        $this->outsider = User::factory()->create(['nama' => 'Orang Asing']);

        $category = Category::factory()->create();

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $category->id,
            'nama_event' => 'Seminar Teknologi 2026',
        ]);

        EventMember::create([
            'event_id' => $this->event->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_hanya_leader_yang_bisa_melihat_anggaran_member_dan_orang_luar_403(): void
    {
        // Leader -> 200
        $responseLeader = $this->actingAs($this->leader)->get(route('acara.anggaran.index', $this->event));
        $responseLeader->assertStatus(200);
        $responseLeader->assertSee('Total Estimasi');

        // Member -> 403 (D-16)
        $responseMember = $this->actingAs($this->member)->get(route('acara.anggaran.index', $this->event));
        $responseMember->assertStatus(403);

        // Orang luar -> 403
        $responseOutsider = $this->actingAs($this->outsider)->get(route('acara.anggaran.index', $this->event));
        $responseOutsider->assertStatus(403);
    }

    public function test_leader_dapat_menambah_mengubah_dan_menghapus_item_anggaran_manual(): void
    {
        // 1. Tambah
        $responseStore = $this->actingAs($this->leader)->post(route('acara.anggaran.store', $this->event), [
            'nama_item' => 'Sewa Gedung Auditorium',
            'jumlah' => 1,
            'harga_satuan' => 5000000,
        ]);

        $responseStore->assertRedirect(route('acara.anggaran.index', $this->event));
        $this->assertDatabaseHas('budgets', [
            'event_id' => $this->event->id,
            'nama_item' => 'Sewa Gedung Auditorium',
            'total' => 5000000,
        ]);

        $budget = $this->event->budgets()->where('nama_item', 'Sewa Gedung Auditorium')->first();

        // 2. Ubah
        $responseUpdate = $this->actingAs($this->leader)->put(route('acara.anggaran.update', [$this->event, $budget]), [
            'nama_item' => 'Sewa Gedung Auditorium (Diskon)',
            'jumlah' => 1,
            'harga_satuan' => 4500000,
        ]);

        $responseUpdate->assertRedirect(route('acara.anggaran.index', $this->event));
        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'nama_item' => 'Sewa Gedung Auditorium (Diskon)',
            'total' => 4500000,
        ]);

        // 3. Hapus
        $responseDestroy = $this->actingAs($this->leader)->delete(route('acara.anggaran.destroy', [$this->event, $budget]));
        $responseDestroy->assertRedirect(route('acara.anggaran.index', $this->event));
        $this->assertDatabaseMissing('budgets', [
            'id' => $budget->id,
        ]);
    }

    public function test_leader_dapat_menambah_item_anggaran_dari_vendor_product(): void
    {
        $vendorUser = User::factory()->create(['role_id' => Role::where('nama', RoleName::Vendor->value)->value('id')]);
        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'nama_vendor' => 'Bali Sound Tech',
            'kategori_vendor' => 'sound_system',
            'alamat' => 'Denpasar',
            'kontak' => '0812345678',
        ]);

        $produk = VendorProduct::create([
            'vendor_id' => $vendor->id,
            'nama_produk' => 'Paket Sound Premium',
            'harga' => 2500000,
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->leader)->post(route('acara.anggaran.dari-vendor', [$this->event, $produk]), [
            'jumlah' => 2,
        ]);

        $response->assertRedirect(route('acara.anggaran.index', $this->event));
        $this->assertDatabaseHas('budgets', [
            'event_id' => $this->event->id,
            'vendor_product_id' => $produk->id,
            'nama_item' => 'Paket Sound Premium',
            'jumlah' => 2,
            'harga_satuan' => 2500000,
            'total' => 5000000,
        ]);
    }
}
