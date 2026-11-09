<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TemplateRequest;
use App\Models\Category;
use App\Models\Template;
use App\Services\TemplateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        $templates = Template::with(['category'])
            ->withCount(['kebutuhan', 'timeline', 'divisi', 'jobdesk'])
            ->latest()
            ->get();

        return view('admin.template.index', compact('templates'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.template.editor', [
            'template' => new Template(),
            'categories' => $categories,
            'isEdit' => false,
        ]);
    }

    public function store(TemplateRequest $request, TemplateService $service): RedirectResponse
    {
        $service->saveTemplate($request->validated());

        return redirect()->route('admin.template.index')
            ->with('status', 'Template master berhasil dibuat.');
    }

    public function edit(Template $template): View
    {
        $template->load(['category', 'items']);
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.template.editor', [
            'template' => $template,
            'categories' => $categories,
            'isEdit' => true,
        ]);
    }

    public function update(TemplateRequest $request, Template $template, TemplateService $service): RedirectResponse
    {
        $service->saveTemplate($request->validated(), $template);

        return redirect()->route('admin.template.index')
            ->with('status', 'Template master berhasil diperbarui.');
    }

    public function duplicate(Template $template, TemplateService $service): RedirectResponse
    {
        $service->duplicateTemplate($template);

        return redirect()->route('admin.template.index')
            ->with('status', 'Template berhasil diduplikasi.');
    }

    public function activate(Template $template, TemplateService $service): RedirectResponse
    {
        $service->activateTemplate($template);

        return redirect()->route('admin.template.index')
            ->with('status', 'Template "'.$template->nama_template.'" telah diaktifkan untuk kategori '.$template->category->nama_kategori.'.');
    }

    public function destroy(Template $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('admin.template.index')
            ->with('status', 'Template berhasil dihapus.');
    }
}

