<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\KategoriVendor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\UpdateProfilUsahaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilUsahaController extends Controller
{
    /**
     * Profil Usaha Vendor (S32, hal. 56; FR-26, FR-03)
     */
    public function edit(Request $request): View
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            abort(403, 'Profil usaha vendor tidak ditemukan.');
        }

        $kategoriOptions = KategoriVendor::cases();

        return view('vendor.profil', compact('vendor', 'kategoriOptions'));
    }

    /**
     * Simpan perubahan profil usaha.
     */
    public function update(UpdateProfilUsahaRequest $request): RedirectResponse
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            abort(403, 'Profil usaha vendor tidak ditemukan.');
        }

        $this->authorize('update', $vendor);

        $vendor->update($request->validated());

        return redirect()->route('usaha.profil')->with('success', 'Profil usaha berhasil diperbarui.');
    }
}
