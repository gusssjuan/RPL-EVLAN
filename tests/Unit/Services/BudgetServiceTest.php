<?php

namespace Tests\Unit\Services;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProduct;
use App\Services\BudgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BudgetService $budgetService;

    protected User $leader;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => 'admin']);
        Role::firstOrCreate(['nama' => 'vendor']);
        Role::firstOrCreate(['nama' => 'user']);

        $this->budgetService = new BudgetService;

        $this->leader = User::factory()->create();
        $category = Category::factory()->create();

        $this->event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_budget_total_dihitung_otomatis_pada_model(): void
    {
        $budget = Budget::create([
            'event_id' => $this->event->id,
            'nama_item' => 'Sewa Kursi',
            'jumlah' => 10,
            'harga_satuan' => 25000,
        ]);

        $this->assertEquals(250000, $budget->total);
    }

    public function test_budget_service_menghitung_agregat_anggaran_dengan_benar(): void
    {
        // 2 item manual
        Budget::create([
            'event_id' => $this->event->id,
            'nama_item' => 'Konsumsi Panitia',
            'jumlah' => 20,
            'harga_satuan' => 30000, // 600.000
        ]);

        Budget::create([
            'event_id' => $this->event->id,
            'nama_item' => 'Spanduk Banner',
            'jumlah' => 2,
            'harga_satuan' => 150000, // 300.000
        ]);

        // 1 item vendor
        $vendorUser = User::factory()->create(['role_id' => Role::where('nama', 'vendor')->value('id')]);
        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'nama_vendor' => 'Audio Pro',
            'kategori_vendor' => 'sound_system',
            'alamat' => 'Jl. Test',
            'kontak' => '0812345678',
        ]);

        $produk = VendorProduct::create([
            'vendor_id' => $vendor->id,
            'nama_produk' => 'Paket Sound Standard',
            'harga' => 1500000,
            'is_aktif' => true,
        ]);

        $this->budgetService->tambahItem($this->event, [
            'vendor_product_id' => $produk->id,
            'nama_item' => $produk->nama_produk,
            'jumlah' => 1,
            'harga_satuan' => $produk->harga, // 1.500.000
        ]);

        $summary = $this->budgetService->forEvent($this->event);

        $this->assertEquals(3, $summary['jumlah_item']);
        $this->assertEquals(2, $summary['manual']);
        $this->assertEquals(1, $summary['dari_vendor']);
        $this->assertEquals(2400000, $summary['total_keseluruhan']);
    }
}
