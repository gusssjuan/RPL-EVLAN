<?php

namespace App\Http\Controllers\Event;

use App\Enums\JenisNotifikasi;
use App\Enums\PeranAnggota;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Division;
use App\Models\Event;
use App\Models\EventMember;
use App\Models\TemplateItem;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisionController extends Controller
{
    /**
     * Tampilkan halaman Struktur Kepanitiaan (S14).
     */
    public function index(Request $request, Event $event): View
    {
        $this->authorize('view', $event);

        $event->load([
            'divisions.members.user',
            'divisions.tasks',
            'members.user',
            'leader',
            'category.templates' => fn ($q) => $q->where('is_aktif', true),
        ]);

        $isLeader = $event->isLeader($request->user());

        // Rekomendasi divisi dari template aktif (D-10)
        $templateDivisions = collect();
        $activeTemplate = $event->category?->templates?->first();
        if ($activeTemplate) {
            $templateDivisions = TemplateItem::where('template_id', $activeTemplate->id)
                ->where('jenis', 'divisi')
                ->orderBy('urutan')
                ->get();
        }

        $existingDivisionNames = $event->divisions->pluck('nama_divisi')->map(fn ($n) => mb_strtolower($n));
        $rekomendasiDitambahkan = $templateDivisions->filter(
            fn ($td) => $existingDivisionNames->contains(mb_strtolower($td->nama_item))
        )->count();

        return view('acara.kepanitiaan.index', compact(
            'event',
            'isLeader',
            'templateDivisions',
            'rekomendasiDitambahkan'
        ));
    }

    /**
     * Tambah divisi baru ke acara.
     */
    public function storeDivision(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'nama_divisi' => ['required', 'string', 'max:100'],
        ]);

        // Validasi: nama divisi unik per acara (Process Spec 4.1)
        $exists = $event->divisions()->whereRaw('LOWER(nama_divisi) = ?', [mb_strtolower($validated['nama_divisi'])])->exists();
        if ($exists) {
            return back()->withErrors(['nama_divisi' => 'Nama divisi "'.$validated['nama_divisi'].'" sudah ada di acara ini.'])->withInput();
        }

        $division = $event->divisions()->create($validated);

        ActivityLogger::log($event, $request->user(), 'buat_divisi', 'menambahkan divisi "'.$division->nama_divisi.'"');

        return redirect()->route('acara.kepanitiaan.index', $event->id)
            ->with('status', 'Divisi "'.$division->nama_divisi.'" berhasil ditambahkan.');
    }

    /**
     * Tambahkan semua rekomendasi divisi dari template sekaligus (D-10).
     */
    public function storeAllRecommendations(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $activeTemplate = $event->category?->templates()?->where('is_aktif', true)->first();
        if (! $activeTemplate) {
            return back()->with('status', 'Tidak ada template aktif untuk kategori ini.');
        }

        $templateDivisions = TemplateItem::where('template_id', $activeTemplate->id)
            ->where('jenis', 'divisi')
            ->orderBy('urutan')
            ->get();

        $existingNames = $event->divisions()->pluck('nama_divisi')->map(fn ($n) => mb_strtolower($n));
        $added = 0;

        foreach ($templateDivisions as $td) {
            if (! $existingNames->contains(mb_strtolower($td->nama_item))) {
                $event->divisions()->create(['nama_divisi' => $td->nama_item]);
                $added++;
            }
        }

        if ($added > 0) {
            ActivityLogger::log($event, $request->user(), 'buat_divisi', 'menambahkan '.$added.' divisi dari rekomendasi template');
        }

        return redirect()->route('acara.kepanitiaan.index', $event->id)
            ->with('status', $added.' divisi rekomendasi berhasil ditambahkan.');
    }

    /**
     * Hapus divisi (ditolak jika ada tugas aktif).
     */
    public function destroyDivision(Event $event, Division $division): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($division->event_id !== $event->id, 404);

        // Tolak jika divisi memiliki tugas (Process Spec 4.1)
        if ($division->tasks()->exists()) {
            return back()->withErrors(['division' => 'Tidak dapat menghapus divisi "'.$division->nama_divisi.'" karena masih memiliki tugas yang terhubung. Pindahkan atau hapus tugas terlebih dahulu.']);
        }

        // Keluarkan anggota dari divisi ini (set division_id null)
        EventMember::where('division_id', $division->id)->update(['division_id' => null]);

        $nama = $division->nama_divisi;
        $division->delete();

        ActivityLogger::log($event, auth()->user(), 'hapus_divisi', 'menghapus divisi "'.$nama.'"');

        return redirect()->route('acara.kepanitiaan.index', $event->id)
            ->with('status', 'Divisi "'.$nama.'" berhasil dihapus.');
    }

    /**
     * Tambah anggota ke acara (Process Spec 4.2).
     */
    public function storeMember(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'division_id' => ['required', 'exists:divisions,id'],
            'peran' => ['required', 'string', 'in:koordinator,anggota'],
        ]);

        // Cari user berdasarkan email (D-15)
        $targetUser = User::where('email', $validated['email'])->first();
        if (! $targetUser) {
            return back()->withErrors(['email' => 'Akun dengan email "'.$validated['email'].'" tidak ditemukan dalam sistem.'])->withInput();
        }

        // User harus role 'user' (bukan admin/vendor)
        if (! $targetUser->isUser()) {
            return back()->withErrors(['email' => 'Hanya akun dengan peran "Pengguna" yang dapat ditambahkan sebagai panitia.'])->withInput();
        }

        // Leader tidak bisa ditambahkan sebagai anggota
        if ($event->isLeader($targetUser)) {
            return back()->withErrors(['email' => 'Ketua panitia sudah otomatis menjadi bagian dari acara ini dan tidak perlu ditambahkan sebagai anggota.'])->withInput();
        }

        // Cek sudah jadi anggota
        if ($event->isMember($targetUser)) {
            return back()->withErrors(['email' => $targetUser->nama.' sudah terdaftar sebagai panitia di acara ini.'])->withInput();
        }

        // Pastikan division milik event ini
        $division = Division::where('id', $validated['division_id'])->where('event_id', $event->id)->first();
        if (! $division) {
            return back()->withErrors(['division_id' => 'Divisi tidak valid.'])->withInput();
        }

        // Buat anggota
        EventMember::create([
            'event_id' => $event->id,
            'user_id' => $targetUser->id,
            'division_id' => $division->id,
            'peran' => PeranAnggota::from($validated['peran']),
        ]);

        // Kirim notifikasi undangan
        AppNotification::create([
            'user_id' => $targetUser->id,
            'event_id' => $event->id,
            'jenis' => JenisNotifikasi::Undangan,
            'pesan' => 'Anda diundang bergabung dalam kepanitiaan acara "'.$event->nama_event.'" di divisi '.$division->nama_divisi.'.',
            'tautan' => '/acara/'.$event->id,
            'waktu_kirim' => now(),
        ]);

        // Catat aktivitas
        ActivityLogger::log(
            $event,
            $request->user(),
            'tambah_anggota',
            'menambahkan '.$targetUser->nama.' sebagai '.PeranAnggota::from($validated['peran'])->label().' divisi '.$division->nama_divisi,
            ['user_id' => $targetUser->id, 'division_id' => $division->id]
        );

        return redirect()->route('acara.kepanitiaan.index', $event->id)
            ->with('status', $targetUser->nama.' berhasil ditambahkan ke divisi '.$division->nama_divisi.'.');
    }

    /**
     * Ubah peran anggota atau pindah divisi.
     */
    public function updateMember(Request $request, Event $event, EventMember $member): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($member->event_id !== $event->id, 404);

        $validated = $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'peran' => ['required', 'string', 'in:koordinator,anggota'],
        ]);

        $division = Division::where('id', $validated['division_id'])->where('event_id', $event->id)->firstOrFail();

        $member->update([
            'division_id' => $division->id,
            'peran' => PeranAnggota::from($validated['peran']),
        ]);

        ActivityLogger::log($event, $request->user(), 'ubah_anggota', 'mengubah peran/divisi '.$member->user->nama);

        return redirect()->route('acara.kepanitiaan.index', $event->id)
            ->with('status', 'Data keanggotaan '.$member->user->nama.' berhasil diperbarui.');
    }

    /**
     * Keluarkan anggota dari acara.
     */
    public function destroyMember(Request $request, Event $event, EventMember $member): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($member->event_id !== $event->id, 404);

        $userName = $member->user->nama;
        $userId = $member->user_id;

        // Anggota yang punya tugas aktif: tugas tetap, division_id null (pilih opsi sederhana per D-10 note)
        $activeTasks = $event->tasks()
            ->where('penerima_id', $userId)
            ->where('status', '!=', 'sudah_diselesaikan')
            ->count();

        $member->delete();

        ActivityLogger::log($event, $request->user(), 'hapus_anggota', 'mengeluarkan '.$userName.' dari kepanitiaan'.($activeTasks > 0 ? ' ('.$activeTasks.' tugas aktif tetap berlaku)' : ''));

        $msg = $userName.' berhasil dikeluarkan dari kepanitiaan.';
        if ($activeTasks > 0) {
            $msg .= ' Perhatian: '.$activeTasks.' tugas aktif miliknya tetap berlaku dan perlu ditugaskan ulang.';
        }

        return redirect()->route('acara.kepanitiaan.index', $event->id)
            ->with('status', $msg);
    }
}
