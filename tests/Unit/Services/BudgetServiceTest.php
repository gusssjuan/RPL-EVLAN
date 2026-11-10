<?php

namespace Tests\Unit\Services;

use App\Enums\RoleName;
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

    protected User $leader;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['nama' => RoleName::Admin->value]);
        Role::firstOrCreate(['nama' => RoleName::User->value]);
        Role::firstOrCreate(['nama' => RoleName::Vendor->value]);

        $this->leader = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    public function test_ringkasan_anggaran_kosong_adalah_nol(): void
    {
        $event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
        ]);

        $ringkasan = BudgetService::ringkasan($event);

        $this->assertSame(0, $ringkasan['total']);
        $this->assertSame(0, $ringkasan['jumlah_item']);
        $this->assertSame(0, $ringkasan['dari_vendor']);
        $this->assertSame(0, $ringkasan['manual']);
    }

    public function test_ringkasan_menghasilkan_total_18_450_000_dengan_11_item(): void
    {
        $event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
        ]);

        $vendor = Vendor::factory()->terverifikasi()->create();

        $hargaProdukVendor = [3500000, 7500000, 2750000, 2000000, 500000];
        foreach ($hargaProdukVendor as $harga) {
            $produk = VendorProduct::factory()->create([
                'vendor_id' => $vendor->id,
                'harga' => $harga,
            ]);

            $event->budgets()->create([
                'vendor_product_id' => $produk->id,
                'nama_item' => $produk->nama_produk,
                'jumlah' => 1,
                'harga_satuan' => $harga,
            ]);
        }

        $itemManual = [
            [2, 200000],
            [2, 500000],
            [1, 300000],
            [1, 200000],
            [1, 100000],
            [4, 50000],
        ];
        foreach ($itemManual as $index => [$jumlah, $harga]) {
            $event->budgets()->create([
                'nama_item' => 'Item manual '.($index + 1),
                'jumlah' => $jumlah,
                'harga_satuan' => $harga,
            ]);
        }

        $ringkasan = BudgetService::ringkasan($event);

        $this->assertSame(18_450_000, $ringkasan['total']);
        $this->assertSame(11, $ringkasan['jumlah_item']);
        $this->assertSame(5, $ringkasan['dari_vendor']);
        $this->assertSame(6, $ringkasan['manual']);
    }

    public function test_tambah_dari_vendor_menghitung_subtotal_otomatis(): void
    {
        $event = Event::factory()->create([
            'leader_id' => $this->leader->id,
            'category_id' => $this->category->id,
        ]);

        $vendor = Vendor::factory()->terverifikasi()->create();
        $produk = VendorProduct::factory()->create([
            'vendor_id' => $vendor->id,
            'nama_produk' => 'Paket Sound System',
            'harga' => 3500000,
        ]);

        $budget = BudgetService::tambahDariVendor($event, $produk, 2);

        $this->assertSame(7000000, $budget->total);
        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'event_id' => $event->id,
            'vendor_product_id' => $produk->id,
            'harga_satuan' => 3500000,
            'total' => 7000000,
        ]);

        $ringkasan = BudgetService::ringkasan($event);
        $this->assertSame(7000000, $ringkasan['total']);
        $this->assertSame(1, $ringkasan['dari_vendor']);
    }
}
