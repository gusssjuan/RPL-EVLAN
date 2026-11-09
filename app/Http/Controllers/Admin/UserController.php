<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleName;
use App\Enums\StatusAkun;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $roleFilter = $request->query('peran', 'semua');
        $statusFilter = $request->query('status', 'semua');
        $search = $request->query('cari');

        $query = User::with('role')->latest();

        if ($roleFilter !== 'semua') {
            $query->whereHas('role', function ($rq) use ($roleFilter) {
                $rq->where('nama', $roleFilter);
            });
        }

        if ($statusFilter !== 'semua' && in_array($statusFilter, ['aktif', 'nonaktif'])) {
            $query->where('status_akun', $statusFilter);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $roles = Role::all();

        return view('admin.pengguna.index', compact('users', 'roles', 'roleFilter', 'statusFilter', 'search'));
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        $currentAdmin = $request->user();

        // 1. Admin dilarang menonaktifkan dirinya sendiri
        if ($user->id === $currentAdmin->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        // 2. Jika hendak menonaktifkan akun admin, pastikan bukan admin aktif terakhir
        if ($user->isAdmin() && $user->status_akun === StatusAkun::Aktif) {
            $adminRole = Role::where('nama', RoleName::Admin->value)->first();
            $activeAdminCount = User::where('role_id', $adminRole->id)
                ->where('status_akun', StatusAkun::Aktif)
                ->count();

            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan administrator terakhir di sistem.');
            }
        }

        // Toggle status
        $newStatus = $user->status_akun === StatusAkun::Aktif
            ? StatusAkun::Nonaktif
            : StatusAkun::Aktif;

        $user->update(['status_akun' => $newStatus]);

        $statusLabel = $newStatus === StatusAkun::Aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('status', 'Akun "'.$user->nama.'" berhasil '.$statusLabel.'.');
    }
}

