<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $vendor = $this->vendorFor($request);

        $vendor->loadCount([
            'products',
            'activeProducts',
        ]);

        $produkTerbaru = $vendor->products()
            ->latest()
            ->take(5)
            ->get();

        $totalNilaiProduk = (int) $vendor->activeProducts()->sum('harga');

        return view('vendor.dashboard', compact(
            'vendor',
            'produkTerbaru',
            'totalNilaiProduk'
        ));
    }

    private function vendorFor(Request $request): Vendor
    {
        $vendor = $request->user()->vendor()->first();

        abort_if(! $vendor, 404, 'Profil usaha vendor tidak ditemukan.');

        return $vendor;
    }
}
