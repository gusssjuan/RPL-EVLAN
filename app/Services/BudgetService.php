<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Event;
use App\Models\VendorProduct;

class BudgetService
{
    /**
     * Ringkasan estimasi anggaran acara (Process Spec 6.0).
     *
     * @return array{total: int, jumlah_item: int, dari_vendor: int, manual: int}
     */
    public static function ringkasan(Event $event): array
    {
        $row = $event->budgets()
            ->selectRaw('
                COUNT(*) as jumlah_item,
                COALESCE(SUM(total), 0) as total,
                SUM(CASE WHEN vendor_product_id IS NOT NULL THEN 1 ELSE 0 END) as dari_vendor,
                SUM(CASE WHEN vendor_product_id IS NULL THEN 1 ELSE 0 END) as manual
            ')
            ->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'jumlah_item' => (int) ($row->jumlah_item ?? 0),
            'dari_vendor' => (int) ($row->dari_vendor ?? 0),
            'manual' => (int) ($row->manual ?? 0),
        ];
    }

    /**
     * Tambahkan produk/jasa vendor ke anggaran acara.
     * total = jumlah × harga_satuan dihitung otomatis oleh model Budget.
     */
    public static function tambahDariVendor(Event $event, VendorProduct $product, int $jumlah): Budget
    {
        return $event->budgets()->create([
            'vendor_product_id' => $product->id,
            'nama_item' => $product->nama_produk,
            'jumlah' => $jumlah,
            'harga_satuan' => $product->harga,
        ]);
    }
}
