<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Event;
use App\Models\VendorProduct;
use Illuminate\Support\Collection;

class BudgetService
{
    /**
     * Hitung ringkasan anggaran untuk suatu acara.
     * Process Spec 6.0: total = jumlah × harga_satuan; total_keseluruhan = Σ total.
     *
     * @return array{
     *   total_keseluruhan: int,
     *   jumlah_item: int,
     *   dari_vendor: int,
     *   manual: int,
     *   items: Collection
     * }
     */
    public function forEvent(Event $event): array
    {
        $items = $event->budgets()
            ->with(['checklist:id,nama_kebutuhan', 'vendorProduct.vendor:id,nama_vendor'])
            ->orderBy('created_at')
            ->get();

        $totalKeseluruhan = $items->sum('total');
        $dariVendor = $items->whereNotNull('vendor_product_id')->count();
        $manual = $items->whereNull('vendor_product_id')->count();

        return [
            'total_keseluruhan' => (int) $totalKeseluruhan,
            'jumlah_item' => $items->count(),
            'dari_vendor' => $dariVendor,
            'manual' => $manual,
            'items' => $items,
        ];
    }

    /**
     * Buat item anggaran baru.
     */
    public function tambahItem(Event $event, array $data): Budget
    {
        // Jika dari vendor product, ambil harga produk
        if (! empty($data['vendor_product_id'])) {
            $produk = VendorProduct::find($data['vendor_product_id']);
            if ($produk) {
                $data['harga_satuan'] = $produk->harga;
                $data['nama_item'] = $data['nama_item'] ?? $produk->nama_produk;
            }
        }

        $data['event_id'] = $event->id;

        return Budget::create($data);
    }

    /**
     * Perbarui item anggaran.
     */
    public function perbaruiItem(Budget $budget, array $data): Budget
    {
        $budget->update($data);

        return $budget->fresh();
    }
}
