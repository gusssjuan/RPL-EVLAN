<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\StatusVerifikasi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorProductRequest;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorProductController extends Controller
{
    public function index(Request $request): View
    {
        $vendor = $this->vendorFor($request);

        $produk = $vendor->products()
            ->orderByDesc('is_aktif')
            ->orderBy('nama_produk')
            ->paginate(10);

        $jumlahAktif = $vendor->products()->where('is_aktif', true)->count();
        $jumlahTotal = $vendor->products()->count();
        $terverifikasi = $vendor->status_verifikasi === StatusVerifikasi::Terverifikasi;

        return view('vendor.produk.index', compact(
            'vendor',
            'produk',
            'jumlahAktif',
            'jumlahTotal',
            'terverifikasi'
        ));
    }

    public function store(VendorProductRequest $request): RedirectResponse
    {
        $vendor = $this->vendorFor($request);

        $this->authorize('manageProducts', $vendor);

        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        $vendor->products()->create($data);

        return redirect()->route('usaha.produk.index')
            ->with('status', 'Produk/jasa baru berhasil ditambahkan.');
    }

    public function update(VendorProductRequest $request, VendorProduct $produk): RedirectResponse
    {
        $vendor = $this->vendorFor($request);

        $this->authorize('manageProducts', $vendor);
        abort_if($produk->vendor_id !== $vendor->id, 404);

        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif');

        $produk->update($data);

        return redirect()->route('usaha.produk.index')
            ->with('status', 'Produk/jasa berhasil diperbarui.');
    }

    public function toggle(Request $request, VendorProduct $produk): RedirectResponse
    {
        $vendor = $this->vendorFor($request);

        $this->authorize('manageProducts', $vendor);
        abort_if($produk->vendor_id !== $vendor->id, 404);

        $produk->update(['is_aktif' => ! $produk->is_aktif]);

        return redirect()->route('usaha.produk.index')
            ->with('status', 'Status produk/jasa berhasil diperbarui.');
    }

    public function destroy(Request $request, VendorProduct $produk): RedirectResponse
    {
        $vendor = $this->vendorFor($request);

        $this->authorize('manageProducts', $vendor);
        abort_if($produk->vendor_id !== $vendor->id, 404);

        if ($produk->budgets()->exists()) {
            return back()->with('error', 'Produk/jasa ini sudah dipakai pada anggaran acara sehingga tidak dapat dihapus. Nonaktifkan saja agar tidak tampil di pencarian.');
        }

        $produk->delete();

        return redirect()->route('usaha.produk.index')
            ->with('status', 'Produk/jasa berhasil dihapus.');
    }

    private function vendorFor(Request $request): Vendor
    {
        $vendor = $request->user()->vendor()->first();

        abort_if(! $vendor, 404, 'Profil usaha vendor tidak ditemukan.');

        return $vendor;
    }
}
