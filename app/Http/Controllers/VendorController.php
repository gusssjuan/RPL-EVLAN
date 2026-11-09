<?php

namespace App\Http\Controllers;

use App\Enums\KategoriVendor;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Pencarian Vendor (S20, hal. 36; mobile 71; FR-25)
     * Hanya vendor terverifikasi yang ditampilkan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $kategori = $request->input('kategori');
        $kota = $request->input('kota');
        $cari = $request->input('cari');
        $tab = $request->input('tab', 'semua'); // 'semua' | 'tersimpan'

        $query = Vendor::terverifikasi()
            ->with(['products' => fn ($q) => $q->where('is_aktif', true), 'savedBy'])
            ->withCount(['products as jumlah_produk' => fn ($q) => $q->where('is_aktif', true)]);

        // Filter tab Tersimpan
        if ($tab === 'tersimpan') {
            $query->whereHas('savedBy', fn ($q) => $q->where('user_id', $user->id));
        }

        // Filter kategori
        if ($kategori && $kategori !== 'semua') {
            $query->where('kategori_vendor', $kategori);
        }

        // Filter kota
        if ($kota) {
            $query->where('kota', 'like', '%'.$kota.'%');
        }

        // Pencarian nama vendor / nama produk
        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_vendor', 'like', '%'.$cari.'%')
                    ->orWhereHas('products', fn ($q2) => $q2->where('nama_produk', 'like', '%'.$cari.'%'));
            });
        }

        $vendors = $query->orderBy('nama_vendor')->paginate(12)->withQueryString();

        // Ambil daftar ID vendor yang sudah disimpan user ini
        $savedVendorIds = $user->savedVendors()->pluck('vendor_id')->toArray();

        $kategoriOptions = KategoriVendor::cases();

        // Daftar kota unik untuk filter
        $kotaList = Vendor::terverifikasi()->whereNotNull('kota')->distinct()->pluck('kota')->sort()->values();

        return view('vendor.index', compact(
            'vendors',
            'savedVendorIds',
            'kategoriOptions',
            'kotaList',
            'kategori',
            'kota',
            'cari',
            'tab'
        ));
    }

    /**
     * Detail Vendor (S21, S21-V, hal. 37–38; FR-25, FR-14)
     */
    public function show(Request $request, Vendor $vendor): View
    {
        // Hanya vendor terverifikasi yang bisa dilihat
        if (! $vendor->status_verifikasi->isVerified()) {
            abort(404);
        }

        $vendor->load(['user', 'products' => fn ($q) => $q->where('is_aktif', true)->orderBy('harga')]);

        $user = $request->user();
        $isSaved = $user->savedVendors()->where('vendor_id', $vendor->id)->exists();

        // Acara aktif milik user (Leader) untuk fitur "Tambah ke Anggaran"
        $acaraAktif = $user->eventsLed()
            ->where('status_event', 'perencanaan')
            ->orderBy('tanggal')
            ->get(['id', 'nama_event', 'tanggal']);

        return view('vendor.show', compact('vendor', 'isSaved', 'acaraAktif'));
    }

    /**
     * Simpan / batal simpan vendor (bookmark).
     */
    public function toggleSave(Request $request, Vendor $vendor): RedirectResponse
    {
        if (! $vendor->status_verifikasi->isVerified()) {
            abort(404);
        }

        $user = $request->user();
        $existing = $user->savedVendors()->where('vendor_id', $vendor->id)->first();

        if ($existing) {
            $user->savedVendors()->detach($vendor->id);
            $message = 'Vendor dihapus dari daftar tersimpan.';
        } else {
            $user->savedVendors()->attach($vendor->id);
            $message = 'Vendor berhasil disimpan.';
        }

        return back()->with('success', $message);
    }
}
