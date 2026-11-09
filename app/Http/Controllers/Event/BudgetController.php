<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreBudgetRequest;
use App\Http\Requests\Event\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\Event;
use App\Models\VendorProduct;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(private readonly BudgetService $budgetService) {}

    /**
     * Estimasi Anggaran (S22, S22-V, hal. 39–40; FR-14, NFR-P4)
     * Leader saja; Member → 403 (D-16).
     */
    public function index(Request $request, Event $event): View
    {
        $this->authorize('viewBudget', $event);

        $filter = $request->input('filter', 'semua'); // 'semua' | 'vendor' | 'manual'
        $cari = $request->input('cari', '');

        $summary = $this->budgetService->forEvent($event);

        // Filter item
        $items = $summary['items'];
        if ($filter === 'vendor') {
            $items = $items->whereNotNull('vendor_product_id');
        } elseif ($filter === 'manual') {
            $items = $items->whereNull('vendor_product_id');
        }
        if ($cari) {
            $items = $items->filter(fn ($b) => str_contains(strtolower($b->nama_item), strtolower($cari)));
        }

        // Checklist acara untuk dropdown "Kaitkan ke kebutuhan"
        $checklists = $event->checklists()->orderBy('urutan')->get(['id', 'nama_kebutuhan']);

        return view('acara.anggaran', compact('event', 'summary', 'items', 'filter', 'cari', 'checklists'));
    }

    /**
     * Tambah item anggaran manual.
     */
    public function store(StoreBudgetRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('viewBudget', $event);

        $this->budgetService->tambahItem($event, $request->validated());

        return redirect()->route('acara.anggaran.index', $event)->with('success', 'Item anggaran berhasil ditambahkan.');
    }

    /**
     * Tambah item anggaran dari vendor product (S21 "Tambahkan ke Anggaran").
     * Dapat dipanggil dari halaman Detail Vendor, Leader saja.
     */
    public function storeFromVendor(Request $request, Event $event, VendorProduct $produk): RedirectResponse
    {
        $this->authorize('viewBudget', $event);

        $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'checklist_id' => ['nullable', 'exists:checklists,id'],
        ], [
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ]);

        $this->budgetService->tambahItem($event, [
            'vendor_product_id' => $produk->id,
            'checklist_id' => $request->input('checklist_id'),
            'nama_item' => $produk->nama_produk,
            'jumlah' => $request->integer('jumlah'),
            'harga_satuan' => $produk->harga,
        ]);

        return redirect()->route('acara.anggaran.index', $event)
            ->with('success', "Produk \"{$produk->nama_produk}\" berhasil ditambahkan ke anggaran.");
    }

    /**
     * Perbarui item anggaran.
     */
    public function update(UpdateBudgetRequest $request, Event $event, Budget $budget): RedirectResponse
    {
        $this->authorize('viewBudget', $event);

        if ($budget->event_id !== $event->id) {
            abort(404);
        }

        $this->budgetService->perbaruiItem($budget, $request->validated());

        return redirect()->route('acara.anggaran.index', $event)->with('success', 'Item anggaran berhasil diperbarui.');
    }

    /**
     * Hapus item anggaran.
     */
    public function destroy(Event $event, Budget $budget): RedirectResponse
    {
        $this->authorize('viewBudget', $event);

        if ($budget->event_id !== $event->id) {
            abort(404);
        }

        $budget->delete();

        return redirect()->route('acara.anggaran.index', $event)->with('success', 'Item anggaran berhasil dihapus.');
    }
}
