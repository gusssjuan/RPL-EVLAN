<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount(['templates', 'events'])
            ->orderBy('nama_kategori')
            ->get();

        return view('admin.kategori.index', compact('categories'));
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('admin.kategori.index')
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function update(CategoryRequest $request, Category $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return redirect()->route('admin.kategori.index')
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $kategori): RedirectResponse
    {
        // Aturan D-23 & Process Spec 2.0: Hapus ditolak jika kategori digunakan acara atau template
        if ($kategori->templates()->exists() || $kategori->events()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh acara atau template.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}
