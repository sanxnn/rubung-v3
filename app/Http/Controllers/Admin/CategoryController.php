<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Category::count(),
            'with_products' => Category::has('products')->count(),
            'with_subcategories' => Category::where('has_subcategories', true)->count(),
        ];

        $query = Category::withCount(['subcategories', 'products']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        $categories = $query->latest()->get();

        return view('admin.category', compact('categories', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s()-]+$/',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'has_subcategories' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
            'name.regex' => 'Nama kategori hanya boleh menggunakan huruf, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',

            'slug.regex' => 'Format slug tidak valid. Hanya boleh menggunakan huruf kecil, angka, dan tanda strip (-).',
            'slug.max' => 'Slug tidak boleh lebih dari 100 karakter.',

            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 500 karakter.',
        ]);

        $slug = $this->processSlug(
            $validated['slug'] ?? null,
            $validated['name'],
            Category::class
        );

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'has_subcategories' => $request->boolean('has_subcategories'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s()-]+$/',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'has_subcategories' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
            'name.regex' => 'Nama kategori hanya boleh menggunakan huruf, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',

            'slug.regex' => 'Format slug tidak valid. Hanya boleh menggunakan huruf kecil, angka, dan tanda strip (-).',
            'slug.max' => 'Slug tidak boleh lebih dari 100 karakter.',
            'slug.unique' => 'Slug ini sudah digunakan oleh kategori lain. Silakan gunakan yang berbeda.',

            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 500 karakter.',
        ]);

        $slug = $this->processSlug(
            $validated['slug'] ?? null,
            $validated['name'],
            Category::class,
            $category->id
        );

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'has_subcategories' => $request->boolean('has_subcategories'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->subcategories()->exists()) {
            return back()->withErrors([
                'delete' => 'Kategori tidak dapat dihapus karena masih memiliki subkategori. Hapus subkategori terlebih dahulu.'
            ]);
        }

        if ($category->products()->exists()) {
            return back()->withErrors([
                'delete' => 'Kategori tidak dapat dihapus karena masih memiliki produk yang terhubung.'
            ]);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function subcategories(Category $category)
    {
        abort_unless(
            $category->has_subcategories,
            403,
            'Kategori ini tidak mengizinkan subkategori.'
        );

        $subcategories = $category
            ->subcategories()
            ->withCount('products')
            ->latest()
            ->get();

        return view(
            'admin.subcategories',
            compact('category', 'subcategories')
        );
    }

    /**
     * Logic pemrosesan slug (manual atau otomatis).
     */
    private function processSlug(
        ?string $inputSlug,
        string $name,
        string $model,
        ?int $excludeId = null,
        ?int $parentId = null
    ): string {
        // Jika slug diisi, gunakan slug tersebut.
        // Jika kosong, generate dari nama.
        $slug = !empty(trim($inputSlug))
            ? Str::slug($inputSlug)
            : Str::slug($name);

        // Fallback jika hasil slug tidak sesuai format.
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            $slug = 'item-' . uniqid();
        }

        // Cek keunikan slug.
        $query = $model::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($parentId) {
            $query->where('category_id', $parentId);
        }

        // Jika slug bentrok, tambahkan timestamp.
        if ($query->exists()) {
            $slug .= '-' . time();
        }

        return $slug;
    }
}
