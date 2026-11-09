<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorDashboardController extends Controller
{
    /**
     * Dashboard Area Usaha Vendor (S31, S31-V, hal. 54–55; FR-26)
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $vendor = $user->vendor()->with('products')->first();

        // Pastikan vendor profile sudah ada (dibuat saat registrasi)
        if (! $vendor) {
            abort(403, 'Profil usaha vendor tidak ditemukan.');
        }

        $jumlahProduk = $vendor->products->count();
        $produkAktif = $vendor->products->where('is_aktif', true)->count();

        return view('vendor.dashboard', compact('vendor', 'jumlahProduk', 'produkAktif'));
    }
}
