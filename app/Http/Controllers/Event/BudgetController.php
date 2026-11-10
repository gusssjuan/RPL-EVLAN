<?php

namespace App\Http\Controllers\Event;

use App\Enums\StatusVerifikasi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\BudgetRequest;
use App\Models\Budget;
use App\Models\Event;
use App\Models\Vendor;
use App\Models\VendorProduct;
use App\Services\ActivityLogger;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $this->authorize('viewBudget', $event); // D-16: Leader only

        $event->load(['category', 'leader']);

        $sumber = $request->query('sumber', 'semua');
        $search = trim((string) $request->query('cari', ''));

        $query = $event->budgets()->with(['checklist', 'vendorProduct.vendor']);

        if ($sumber === 'vendor') {
            $query->whereNotNull('vendor_product_id');
        } elseif ($sumber === 'manual') {
            $query->whereNull('vendor_product_id');
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_item', 'like', "%{$search}%")
                    ->orWhereHas('checklist', fn ($cq) => $cq->where('nama_kebutuhan', 'like', "%{$search}%"));
            });
        }

        $budgets = $query->orderByDesc('vendor_product_id')->orderBy('nama_item')->get();

        $ringkasan = BudgetService::ringkasan($event);
        $checklists = $event->checklists()->orderBy('urutan')->get();

        return view('acara.anggaran.index', compact(
            'event',
            'budgets',
            'ringkasan',
            'checklists',
            'sumber',
            'search'
        ));
    }

    public function store(BudgetRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('manage', $event);

        $validated = $request->validated();

        $event->budgets()->create([
            'checklist_id' => $validated['checklist_id'] ?? null,
            'nama_item' => $validated['nama_item'],
            'jumlah' => $validated['jumlah'],
            'harga_satuan' => $validated['harga_satuan'],
        ]);

        ActivityLogger::log(
            $event,
            $request->user(),
            'tambah_anggaran',
            'menambahkan item anggaran "'.$validated['nama_item'].'"'
        );

        return redirect()->route('acara.anggaran', $event->id)
            ->with('status', 'Item anggaran berhasil ditambahkan.');
    }

    public function update(BudgetRequest $request, Event $event, Budget $budget): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($budget->event_id !== $event->id, 404);

        $validated = $request->validated();

        $budget->update([
            'checklist_id' => $validated['checklist_id'] ?? null,
            'nama_item' => $validated['nama_item'],
            'jumlah' => $validated['jumlah'],
            'harga_satuan' => $validated['harga_satuan'],
        ]);

        return redirect()->route('acara.anggaran', $event->id)
            ->with('status', 'Item anggaran berhasil diperbarui.');
    }

    public function destroy(Request $request, Event $event, Budget $budget): RedirectResponse
    {
        $this->authorize('manage', $event);

        abort_if($budget->event_id !== $event->id, 404);

        $nama = $budget->nama_item;
        $budget->delete();

        ActivityLogger::log(
            $event,
            $request->user(),
            'hapus_anggaran',
            'menghapus item anggaran "'.$nama.'"'
        );

        return redirect()->route('acara.anggaran', $event->id)
            ->with('status', 'Item anggaran berhasil dihapus.');
    }

    public function storeFromVendor(Request $request, Vendor $vendor): RedirectResponse
    {
        abort_unless($vendor->status_verifikasi === StatusVerifikasi::Terverifikasi, 404);

        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'vendor_product_id' => ['required', 'integer', 'exists:vendor_products,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ], [
            'event_id.required' => 'Acara wajib dipilih.',
            'vendor_product_id.required' => 'Produk/jasa wajib dipilih.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);

        $event = Event::findOrFail($validated['event_id']);
        $this->authorize('manage', $event);

        $product = VendorProduct::where('vendor_id', $vendor->id)
            ->where('is_aktif', true)
            ->findOrFail($validated['vendor_product_id']);

        $budget = BudgetService::tambahDariVendor($event, $product, (int) $validated['jumlah']);

        ActivityLogger::log(
            $event,
            $request->user(),
            'tambah_anggaran',
            'menambahkan "'.$product->nama_produk.'" dari vendor '.$vendor->nama_vendor.' ke anggaran',
            ['budget_id' => $budget->id, 'vendor_id' => $vendor->id]
        );

        return redirect()->route('acara.anggaran', $event->id)
            ->with('status', '"'.$product->nama_produk.'" berhasil ditambahkan ke anggaran acara.');
    }
}
