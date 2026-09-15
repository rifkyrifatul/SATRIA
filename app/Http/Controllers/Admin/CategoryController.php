<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    private function authorizeSetum()
    {
        $user = auth()->user();
        if ($user->role !== 'super_admin') {
            abort_unless(
                $user->role === 'admin' && $user->admin_level === 'admin_3',
                403,
                'Hanya Admin SETUM yang memiliki akses untuk kelola alur pengajuan.'
            );
        }
    }

    public function index(Request $request)
    {
        $this->authorizeSetum();
        $query = Category::withCount('letters')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('review_type')) {
            $query->where('review_type', $request->review_type);
        }

        $categories = $query->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorizeSetum();
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'review_type' => 'required|in:langsung_admin_3,bertahap',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        $prefix = auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin';
        return redirect()->route($prefix . '.categories.index')->with('success', 'Alur pengajuan berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $this->authorizeSetum();
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'review_type' => 'required|in:langsung_admin_3,bertahap',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        $prefix = auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin';
        return redirect()->route($prefix . '.categories.index')->with('success', 'Alur pengajuan berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $this->authorizeSetum();
        $prefix = auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin';

        if ($category->letters()->count() > 0) {
            return redirect()->route($prefix . '.categories.index')
                ->with('error', 'Tidak dapat menghapus alur pengajuan yang sudah digunakan pada surat.');
        }

        $category->delete();

        return redirect()->route($prefix . '.categories.index')->with('success', 'Alur pengajuan berhasil dihapus.');
    }
}
