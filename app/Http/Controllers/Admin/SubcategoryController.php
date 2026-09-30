<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    // STRICT: Menerima Category dari route untuk memastikan ownership
    public function store(Request $request, Category $category)
    {
        abort_unless($category->has_subcategories, 403, 'Kategori ini tidak mengizinkan subkategori.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s()-]+$/', Rule::unique('subcategories', 'name')->where('category_id', $category->id),],
            'slug' => 'nullable|string|max:100|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
        ], [
            'name.unique' => 'Nama sudah digunakan.',
            'name.regex' => 'Nama subkategori hanya boleh menggunakan huruf, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',
            'name.required' => 'Nama subkategori wajib diisi.',
            'name.max' => 'Nama subkategori tidak boleh lebih dari 100 karakter.',
            'slug.regex' => 'Format slug tidak valid. Hanya boleh menggunakan huruf kecil, angka, dan tanda strip (-).',
            'slug.max' => 'Slug tidak boleh lebih dari 100 karakter.',
        ]);

        $slug = $this->processSlug(
            $validated['slug'] ?? null,
            $validated['name'],
            Subcategory::class,
            null,
            $category->id
        );

        $category->subcategories()->create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return back()->with('success', 'Subkategori berhasil ditambahkan.');
    }

    // STRICT: Memastikan subcategory yang diupdate benar-benar milik category di URL
    public function update(Request $request, Category $category, Subcategory $subcategory)
    {
        abort_unless($category->has_subcategories, 403);
        abort_if($subcategory->category_id !== $category->id, 403, 'Aksi tidak valid.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s()-]+$/', Rule::unique('subcategories', 'name')->where('category_id', $category->id)->ignore($subcategory->id),],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('subcategories', 'slug')->ignore($subcategory->id)->where('category_id', $category->id),
            ],
        ], [
            'name.unique' => 'Nama sudah digunakan.',
            'name.regex' => 'Nama subkategori hanya boleh menggunakan huruf, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',
            'name.required' => 'Nama subkategori wajib diisi.',
            'name.max' => 'Nama subkategori tidak boleh lebih dari 100 karakter.',
            'slug.regex' => 'Format slug tidak valid. Hanya boleh menggunakan huruf kecil, angka, dan tanda strip (-).',
            'slug.max' => 'Slug tidak boleh lebih dari 100 karakter.',
            'slug.unique' => 'Slug ini sudah digunakan dalam kategori yang sama. Silakan gunakan yang berbeda.',
        ]);

        $slug = $this->processSlug(
            $validated['slug'] ?? null,
            $validated['name'],
            Subcategory::class,
            $subcategory->id,
            $category->id
        );

        $subcategory->update([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return back()->with('success', 'Subkategori berhasil diperbarui.');
    }

    // STRICT: Memastikan subcategory yang dihapus benar-benar milik category di URL
    public function destroy(Category $category, Subcategory $subcategory)
    {
        abort_if($subcategory->category_id !== $category->id, 403, 'Aksi tidak valid.');

        if ($subcategory->products()->exists()) {
            return back()->withErrors([
                'delete' => 'Subkategori tidak dapat dihapus karena masih memiliki produk yang terhubung.'
            ]);
        }

        $subcategory->delete();
        return back()->with('success', 'Subkategori berhasil dihapus.');
    }

    private function processSlug(?string $inputSlug, string $name, string $model, ?int $excludeId = null, ?int $parentId = null): string
    {
        $slug = !empty(trim($inputSlug)) ? Str::slug($inputSlug) : Str::slug($name);

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            $slug = 'sub-' . uniqid();
        }

        $query = $model::where('slug', $slug);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        if ($parentId) $query->where('category_id', $parentId);

        if ($query->exists()) {
            $slug .= '-' . time();
        }

        return $slug;
    }
}
