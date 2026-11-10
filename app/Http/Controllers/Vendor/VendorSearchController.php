<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\KategoriVendor;
use App\Enums\StatusEvent;
use App\Enums\StatusVerifikasi;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorSearchController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $tab = $request->query('tab', 'semua');
        $search = trim((string) $request->query('cari', ''));
        $kategori = $request->query('kategori', 'semua');
        $kota = $request->query('kota');
        $kategoriValid = collect(KategoriVendor::cases())->pluck('value')->all();

        $query = Vendor::query()
            ->terverifikasi()
            ->with(['products' => fn ($q) => $q->where('is_aktif', true)]);

        if ($tab === 'tersimpan') {
            $query->whereHas('savedBy', fn ($q) => $q->where('users.id', $user->id));
        }

        if (in_array($kategori, $kategoriValid, true)) {
            $query->where('kategori_vendor', $kategori);
        }

        if (! empty($kota)) {
            $query->where('kota', $kota);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_vendor', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('products', function ($pq) use ($search) {
                        $pq->where('nama_produk', 'like', "%{$search}%");
                    });
            });
        }

        $vendors = $query->orderBy('nama_vendor')->paginate(9)->withQueryString();

        $savedIds = $user->savedVendors()
            ->pluck('vendors.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $counts = [
            'semua' => Vendor::terverifikasi()->count(),
            'tersimpan' => $user->savedVendors()->count(),
        ];

        $daftarKota = Vendor::terverifikasi()
            ->whereNotNull('kota')
            ->where('kota', '!=', '')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');

        return view('vendor.cari.index', compact(
            'vendors',
            'savedIds',
            'counts',
            'daftarKota',
            'tab',
            'search',
            'kategori',
            'kota'
        ));
    }

    public function show(Request $request, Vendor $vendor): View
    {
        abort_unless($vendor->status_verifikasi === StatusVerifikasi::Terverifikasi, 404);

        $user = $request->user();

        $vendor->load([
            'user',
            'products' => fn ($q) => $q->where('is_aktif', true)->orderBy('harga'),
        ]);

        $tersimpan = $user->savedVendors()
            ->where('vendors.id', $vendor->id)
            ->exists();

        $acaraLeader = $user->eventsLed()
            ->where('status_event', StatusEvent::Perencanaan)
            ->orderBy('tanggal')
            ->get(['id', 'nama_event', 'tanggal']);

        $waNumber = preg_replace('/[^0-9]/', '', $vendor->kontak);
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62'.substr($waNumber, 1);
        }

        $kategoriLabel = KategoriVendor::tryFrom($vendor->kategori_vendor)?->label() ?? $vendor->kategori_vendor;

        return view('vendor.cari.show', compact(
            'vendor',
            'tersimpan',
            'acaraLeader',
            'waNumber',
            'kategoriLabel'
        ));
    }

    public function simpan(Request $request, Vendor $vendor): RedirectResponse
    {
        abort_unless($vendor->status_verifikasi === StatusVerifikasi::Terverifikasi, 404);

        $request->user()->savedVendors()->syncWithoutDetaching([$vendor->id]);

        return back()->with('status', 'Vendor "'.$vendor->nama_vendor.'" berhasil disimpan.');
    }

    public function batalSimpan(Request $request, Vendor $vendor): RedirectResponse
    {
        $request->user()->savedVendors()->detach($vendor->id);

        return back()->with('status', 'Vendor "'.$vendor->nama_vendor.'" dihapus dari daftar tersimpan.');
    }
}
