<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventCategoryRequest;
use App\Http\Requests\Event\EventDetailRequest;
use App\Models\Category;
use App\Models\Template;
use App\Services\EventPlanningService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventWizardController extends Controller
{
    /**
     * Langkah 1/3: Pilih Kategori Acara & Pratinjau Template (S10).
     */
    public function stepCategory(Request $request): View
    {
        $categories = Category::with(['activeTemplate.kebutuhan', 'activeTemplate.timeline', 'activeTemplate.divisi', 'activeTemplate.jobdesk'])
            ->orderBy('nama_kategori')
            ->get();

        $selectedCategoryId = old('category_id', session('event_draft.category_id', $categories->first()?->id));

        $selectedCategory = $categories->firstWhere('id', $selectedCategoryId) ?? $categories->first();

        return view('acara.wizard.kategori', compact('categories', 'selectedCategory', 'selectedCategoryId'));
    }

    /**
     * Simpan Kategori ke Session (D-25).
     */
    public function storeCategory(EventCategoryRequest $request): RedirectResponse
    {
        $draft = session('event_draft', []);
        $draft['category_id'] = (int) $request->category_id;
        session(['event_draft' => $draft]);

        return redirect()->route('acara.buat.detail');
    }

    /**
     * Langkah 2/3: Isi Detail Acara (S11, S11-V).
     */
    public function stepDetail(): View|RedirectResponse
    {
        $categoryId = session('event_draft.category_id');

        if (! $categoryId) {
            return redirect()->route('acara.buat.kategori');
        }

        $category = Category::findOrFail($categoryId);
        $draft = session('event_draft', []);

        return view('acara.wizard.detail', compact('category', 'draft'));
    }

    /**
     * Simpan Detail Acara ke Session.
     */
    public function storeDetail(EventDetailRequest $request): RedirectResponse
    {
        $draft = session('event_draft', []);
        $draft = array_merge($draft, $request->validated());
        session(['event_draft' => $draft]);

        return redirect()->route('acara.buat.rancangan');
    }

    /**
     * Langkah 3/3: Rancangan dan Pratinjau Lengkap Sebelum Dibuat (S11A, S11A-V).
     */
    public function stepRancangan(): View|RedirectResponse
    {
        $draft = session('event_draft', []);

        if (empty($draft['category_id']) || empty($draft['nama_event']) || empty($draft['tanggal'])) {
            return redirect()->route('acara.buat.detail');
        }

        $category = Category::findOrFail($draft['category_id']);
        $template = Template::with(['kebutuhan', 'timeline', 'divisi', 'jobdesk'])
            ->where('category_id', $category->id)
            ->where('is_aktif', true)
            ->first();

        if (! $template) {
            $template = Template::with(['kebutuhan', 'timeline', 'divisi', 'jobdesk'])
                ->where('category_id', $category->id)
                ->first();
        }

        $eventDate = Carbon::parse($draft['tanggal']);

        // Persiapkan pratinjau tanggal timeline
        $timelinePreview = [];
        $hasHariH = false;
        if ($template && $template->timeline) {
            foreach ($template->timeline as $item) {
                $offset = (int) $item->offset_hari;
                if ($offset === 0) {
                    $hasHariH = true;
                }
                $tgl = $eventDate->copy()->subDays($offset);
                $timelinePreview[] = [
                    'label' => $offset === 0 ? 'Hari H' : 'H-'.$offset,
                    'offset_hari' => $offset,
                    'tanggal' => $tgl->isoFormat('D MMMM Y'),
                    'tanggal_raw' => $tgl->toDateString(),
                    'kegiatan' => $item->nama_item,
                    'is_today' => $tgl->isToday(),
                ];
            }
        }

        if (! $hasHariH) {
            $timelinePreview[] = [
                'label' => 'Hari H',
                'offset_hari' => 0,
                'tanggal' => $eventDate->isoFormat('D MMMM Y'),
                'tanggal_raw' => $eventDate->toDateString(),
                'kegiatan' => 'Hari Pelaksanaan Acara',
                'is_today' => $eventDate->isToday(),
            ];
        }

        return view('acara.wizard.rancangan', compact(
            'draft',
            'category',
            'template',
            'timelinePreview',
            'eventDate'
        ));
    }

    /**
     * Eksekusi Buat Acara dari Template (T4.3).
     */
    public function storeEvent(Request $request, EventPlanningService $service): RedirectResponse
    {
        $draft = session('event_draft', []);

        if (empty($draft['category_id']) || empty($draft['nama_event']) || empty($draft['tanggal'])) {
            return redirect()->route('acara.buat.kategori')
                ->with('error', 'Sesi draf pembuatan acara telah berakhir. Silakan pilih kembali.');
        }

        $event = $service->createFromTemplate($request->user(), $draft);

        session()->forget('event_draft');

        return redirect()->route('acara.ringkasan', $event->id)
            ->with('status', 'Acara "'.$event->nama_event.'" berhasil dibuat!');
    }
}
