<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleName;
use App\Enums\StatusAkun;
use App\Enums\StatusVerifikasi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            $isVendor = $request->tipe === 'vendor';
            $roleName = $isVendor ? RoleName::Vendor : RoleName::User;
            $role = Role::where('nama', $roleName->value)->firstOrFail();

            $user = User::create([
                'role_id' => $role->id,
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'no_telepon' => $request->no_telepon,
                'status_akun' => StatusAkun::Aktif,
            ]);

            if ($isVendor) {
                Vendor::create([
                    'user_id' => $user->id,
                    'nama_vendor' => $request->nama_vendor,
                    'kategori_vendor' => $request->kategori_vendor,
                    'alamat' => $request->alamat,
                    'kontak' => $request->kontak,
                    'status_verifikasi' => StatusVerifikasi::Menunggu,
                ]);
            }

            return $user;
        });

        Auth::login($user);

        if ($user->isVendor()) {
            return redirect('/usaha')->with('info', 'Akun vendor akan diverifikasi Admin sebelum produk dapat ditambahkan.');
        }

        return redirect(route('dashboard', [], false));
    }
}

