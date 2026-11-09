<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JenisNotifikasi;
use App\Enums\StatusVerifikasi;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'semua');
        $search = $request->query('cari');

        $query = Vendor::with('user')->latest();

        if ($status !== 'semua' && in_array($status, ['menunggu', 'terverifikasi', 'ditolak'])) {
            $query->where('status_verifikasi', $status);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_vendor', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $vendors = $query->paginate(10)->withQueryString();

        $counts = [
            'semua' => Vendor::count(),
            'menunggu' => Vendor::where('status_verifikasi', StatusVerifikasi::Menunggu)->count(),
            'terverifikasi' => Vendor::where('status_verifikasi', StatusVerifikasi::Terverifikasi)->count(),
            'ditolak' => Vendor::where('status_verifikasi', StatusVerifikasi::Ditolak)->count(),
        ];

        return view('admin.vendor.index', compact('vendors', 'counts', 'status', 'search'));
    }

    public function approve(Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'status_verifikasi' => StatusVerifikasi::Terverifikasi,
            'alasan_penolakan' => null,
            'verified_at' => now(),
        ]);

        // Process Spec 6.0: Notifikasi verifikasi vendor
        AppNotification::create([
            'user_id' => $vendor->user_id,
            'jenis' => JenisNotifikasi::VerifikasiVendor,
            'pesan' => 'Selamat! Pendaftaran usaha vendor "'.$vendor->nama_vendor.'" telah disetujui Admin. Anda sekarang dapat menambahkan produk dan jasa.',
            'tautan' => '/usaha',
            'waktu_kirim' => now(),
        ]);

        return redirect()->route('admin.verifikasi-vendor.index')
            ->with('status', 'Vendor "'.$vendor->nama_vendor.'" berhasil diverifikasi.');
    }

    public function reject(Request $request, Vendor $vendor): RedirectResponse
    {
        $request->validate([
            'alasan_penolakan' => ['required', 'string', 'max:500'],
        ]);

        $vendor->update([
            'status_verifikasi' => StatusVerifikasi::Ditolak,
            'alasan_penolakan' => $request->alasan_penolakan,
            'verified_at' => null,
        ]);

        // Process Spec 6.0: Notifikasi penolakan vendor
        AppNotification::create([
            'user_id' => $vendor->user_id,
            'jenis' => JenisNotifikasi::VerifikasiVendor,
            'pesan' => 'Pendaftaran usaha vendor "'.$vendor->nama_vendor.'" ditolak. Alasan: '.$request->alasan_penolakan,
            'tautan' => '/usaha/profil',
            'waktu_kirim' => now(),
        ]);

        return redirect()->route('admin.verifikasi-vendor.index')
            ->with('status', 'Pendaftaran vendor "'.$vendor->nama_vendor.'" ditolak.');
    }
}

