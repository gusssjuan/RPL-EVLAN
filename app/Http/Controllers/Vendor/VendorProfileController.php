<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\KategoriVendor;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $vendor = $request->user()->vendor()->firstOrFail();
        $kategori = KategoriVendor::cases();

        return view('vendor.profil.edit', compact('vendor', 'kategori'));
    }

    public function update(VendorProfileRequest $request): RedirectResponse
    {
        $vendor = $request->user()->vendor()->firstOrFail();

        $this->authorize('update', $vendor);

        $vendor->update($request->validated());

        return redirect()->route('usaha.profil.edit')
            ->with('status', 'Profil usaha berhasil diperbarui.');
    }
}
