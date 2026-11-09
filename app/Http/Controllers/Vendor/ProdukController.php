<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\StoreProdukRequest;
use App\Http\Requests\Vendor\UpdateProdukRequest;
use App\Models\VendorProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdukController extends Controller
{
    /**
     * Kelola Produk/Jasa Vendor (S33, S33-V, hal. 57–58; FR-26)
     */
    public function index(Request $request): View
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            abort(403, 'Profil usaha vendor tidak ditemukan.');
        }

        $produk = $vendor->products()->orderBy('is_aktif', 'desc')->orderBy('created_at', 'desc')->get();

        return view('vendor.produk.index', compact('vendor', 'produk'));
    }

    /**
     * Tambah produk baru. Terkunci sebelum terverifikasi (D-23).
     */
    public function store(StoreProdukRequest $request): RedirectResponse
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            abort(403, 'Profil usaha vendor tidak ditemukan.');
        }

        $this->authorize('manageProducts', $vendor);

        $vendor->products()->create($request->validated());

        return redirect()->route('usaha.produk.index')->with('success', 'Produk/jasa berhasil ditambahkan.');
    }

    /**
     * Perbarui produk.
     */
    public function update(UpdateProdukRequest $request, VendorProduct $produk): RedirectResponse
    {
        $this->authorize('manageProducts', $produk->vendor);

        // Pastikan produk milik vendor yang login
        if ($produk->vendor->user_id !== $request->user()->id) {
            abort(403);
        }

        $produk->update($request->validated());

        return redirect()->route('usaha.produk.index')->with('success', 'Produk/jasa berhasil diperbarui.');
    }

    /**
     * Hapus produk (hanya jika tidak dipakai di budgets — D-23).
     */
    public function destroy(Request $request, VendorProduct $produk): RedirectResponse
    {
        $this->authorize('manageProducts', $produk->vendor);

        if ($produk->vendor->user_id !== $request->user()->id) {
            abort(403);
        }

        // D-23: vendor yang produknya dipakai di budgets → nonaktifkan saja, jangan hapus
        if ($produk->budgets()->exists()) {
            $produk->update(['is_aktif' => false]);

            return redirect()->route('usaha.produk.index')
                ->with('info', 'Produk ini telah digunakan di anggaran acara, sehingga dinonaktifkan dan tidak dapat dihapus.');
        }

        $produk->delete();

        return redirect()->route('usaha.produk.index')->with('success', 'Produk/jasa berhasil dihapus.');
    }
}
