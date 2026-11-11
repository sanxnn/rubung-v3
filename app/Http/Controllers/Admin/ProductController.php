<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBundleItem;
use App\Models\ProductVariant;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory', 'variants'])
            ->whereDoesntHave('variants.bundleComponents');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->latest()->paginate(15);

        $categories = Category::with('subcategories')
            ->where('name', '!=', 'Bundling')
            ->get();

        return view('admin.product', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'name' => ['required', 'string', 'max:150', 'regex:/^[A-Za-z\s()-]+$/', Rule::unique('products', 'name')],
            'slug' => 'nullable|string|max:150|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:products,slug',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.regex' => 'Nama produk hanya boleh menggunakan huruf, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',
            'name.unique' => 'Nama sudah digunakan.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'name.required' => 'Nama produk wajib diisi.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda strip (-).',
            'slug.unique' => 'Slug ini sudah digunakan.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if (Category::findOrFail($validated['category_id'])->slug === 'bundling') {
            return back()->withErrors(['category_id' => 'Paket harus dibuat melalui halaman Paket Bundling.'])->withInput();
        }

        if ($request->filled('subcategory_id')) {
            $sub = Subcategory::find($request->subcategory_id);

            // 1. Cek apakah subkategori benar-benar milik kategori yang dipilih
            if (! $sub || $sub->category_id != $validated['category_id']) {
                return back()->withErrors(['subcategory_id' => 'Subkategori tidak valid atau tidak milik kategori yang dipilih.'])->withInput();
            }

            // 2. Cek apakah kategori induk mengizinkan subkategori
            $cat = Category::find($validated['category_id']);
            if (! $cat->has_subcategories) {
                return back()->withErrors(['subcategory_id' => 'Kategori yang dipilih tidak mendukung fitur subkategori.'])->withInput();
            }
        }
        // ==========================================

        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? null, $validated['name']);
        $newImage = null;
        try {
            if ($request->hasFile('image')) {
                $newImage = $request->file('image')->store('products', 'public');
                $validated['image'] = $newImage;
            }

            Product::create($validated);
        } catch (\Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }
            Log::error('Product Store Error: '.$e->getMessage());

            return back()->withErrors(['system' => 'Gagal menyimpan produk.'])->withInput();
        }

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        abort_if($product->variants()->whereHas('bundleComponents')->exists(), 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'name' => ['required', 'string', 'max:150', 'regex:/^[A-Za-z\s()-]+$/', Rule::unique('products', 'name')->ignore($product->id)],
            'slug' => ['nullable', 'string', 'max:150', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('products', 'slug')->ignore($product->id)],
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.regex' => 'Nama produk hanya boleh menggunakan huruf, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',
            'name.unique' => 'Nama sudah digunakan.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'name.required' => 'Nama produk wajib diisi.',
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda strip (-).',
            'slug.unique' => 'Slug ini sudah digunakan oleh produk lain.',
        ]);

        if (Category::findOrFail($validated['category_id'])->slug === 'bundling') {
            return back()->withErrors(['category_id' => 'Paket harus dikelola melalui halaman Paket Bundling.'])->withInput();
        }

        if ($request->filled('subcategory_id')) {
            $sub = Subcategory::find($request->subcategory_id);
            if (! $sub || $sub->category_id != $validated['category_id']) {
                return back()->withErrors(['subcategory_id' => 'Subkategori tidak valid atau tidak milik kategori yang dipilih.'])->withInput();
            }
            $cat = Category::find($validated['category_id']);
            if (! $cat->has_subcategories) {
                return back()->withErrors(['subcategory_id' => 'Kategori yang dipilih tidak mendukung fitur subkategori.'])->withInput();
            }
        }
        // ==========================================

        $validated['slug'] = $this->uniqueSlug($validated['slug'] ?? null, $validated['name'], $product->id);
        $oldImage = $product->image;
        $newImage = null;
        try {
            if ($request->hasFile('image')) {
                $newImage = $request->file('image')->store('products', 'public');
                $validated['image'] = $newImage;
            }

            $product->update($validated);
        } catch (\Throwable $e) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }
            Log::error('Product Update Error: '.$e->getMessage());

            return back()->withErrors(['system' => 'Gagal memperbarui produk.'])->withInput();
        }

        if ($newImage && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->variants()->whereHas('bundleComponents')->exists(), 403);
        if ($this->hasPendingOrder($product)) {
            return back()->withErrors(['delete' => 'Produk masih digunakan oleh pesanan yang menunggu pembayaran.']);
        }
        if ($product->variants()->whereHas('bundleAsComponent')->exists()) {
            return back()->withErrors(['delete' => 'Produk masih digunakan sebagai komponen paket.']);
        }

        $image = $product->image;
        $product->delete();
        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function variants(Product $product)
    {
        abort_if($product->variants()->whereHas('bundleComponents')->exists(), 403);
        // Ambil varian dengan pagination (15 per halaman)
        $variants = $product->variants()
            ->with(['bundleComponents.componentVariant'])
            ->latest()
            ->paginate(15);

        // Ambil SEMUA varian produk ini (tanpa paginate) untuk dropdown komponen
        $availableComponents = $product->variants()->get();

        return view('admin.variant', compact('product', 'variants', 'availableComponents'));
    }

    /**
     * Menampilkan halaman khusus Daftar Paket Bundling
     */
    public function bundles(Request $request)
    {
        $query = Product::with([
            'category',
            'variants.bundleComponents.componentVariant.product',
        ])
            ->whereHas('variants', function ($query) {
                $query->whereHas('bundleComponents');
            });

        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%'.$request->search.'%'
            );
        }

        $bundles = $query->latest()->paginate(15);

        $availableComponents = ProductVariant::whereDoesntHave(
            'bundleComponents'
        )
            ->whereHas('product.category', function ($query) {
                $query->where('name', 'Bahan');
            })
            ->with('product')
            ->orderBy('product_id')
            ->get();

        return view(
            'admin.bundles',
            compact(
                'bundles',
                'availableComponents'
            )
        );
    }

    private function getBundlingCategory()
    {
        return Category::firstOrCreate(['slug' => 'bundling'], [
            'name' => 'Bundling',
            'description' => 'Paket bundling dari beberapa varian bahan.',
            'has_subcategories' => false,
        ]);
    }

    /**
     * Menyimpan Paket Bundling baru (1 langkah: Product + Variant + BundleItems)
     */
    public function storeBundle(Request $request)
    {
        $requestData = $request->all();

        $cleanComponents = [];

        if (isset($requestData['components']) && is_array($requestData['components'])) {
            foreach ($requestData['components'] as $component) {
                if (! empty($component['variant_id']) && ! empty($component['quantity'])) {
                    $cleanComponents[] = [
                        'variant_id' => (int) $component['variant_id'],
                        'quantity' => (int) $component['quantity'],
                    ];
                }
            }
        }
        $requestData['components'] = $cleanComponents;

        unset($requestData['category_id']);

        $request->replace($requestData);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'regex:/^[A-Za-z\s()-]+$/',
                Rule::unique('products', 'name'),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:products,slug',
            ],

            'description' => 'nullable|string|max:1000',

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],

            'bundle_sku' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                'unique:product_variants,sku',
            ],

            'bundle_price' => 'required|integer|min:0',

            'components' => [
                'required',
                'array',
                'min:1',
            ],

            'components.*.variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'components.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'name.regex' => 'Nama paket hanya boleh menggunakan huruf, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',

            'name.unique' => 'Nama sudah digunakan.',

            'name.required' => 'Nama paket wajib diisi.',

            'bundle_sku.required' => 'SKU paket wajib diisi.',

            'bundle_sku.regex' => 'SKU hanya boleh huruf besar, angka, dan strip (-).',

            'bundle_sku.unique' => 'SKU paket sudah digunakan.',

            'bundle_price.required' => 'Harga paket wajib diisi.',

            'bundle_price.min' => 'Harga tidak boleh negatif.',

            'components.required' => 'Paket wajib memiliki minimal 1 komponen.',

            'components.min' => 'Paket wajib memiliki minimal 1 komponen.',
        ]);

        // 3. STRICT BUSINESS LOGIC
        $componentIds = collect($validated['components'])->pluck('variant_id');

        // Anti-duplikasi komponen
        if ($componentIds->duplicates()->isNotEmpty()) {
            return back()->withErrors(['components' => 'Komponen tidak boleh diduplikasi dalam satu paket.'])->withInput();
        }

        $invalidComponents = ProductVariant::whereIn(
            'id',
            $componentIds
        )
            ->whereHas('product.category', function ($query) {
                $query->where('name', '!=', 'Bahan');
            })
            ->exists();

        if ($invalidComponents) {
            return back()
                ->withErrors([
                    'components' => 'Komponen bundling hanya boleh berasal dari kategori Bahan.',
                ])
                ->withInput();
        }

        $nestedBundle = ProductVariant::whereIn(
            'id',
            $componentIds
        )
            ->whereHas('bundleComponents')
            ->exists();

        if ($nestedBundle) {
            return back()
                ->withErrors([
                    'components' => 'Komponen tidak boleh berupa paket bundling lain.',
                ])
                ->withInput();
        }

        $bundlingCategory = $this->getBundlingCategory();

        DB::beginTransaction();
        $imagePath = null;

        try {
            $slug = $this->uniqueSlug($validated['slug'] ?? null, $validated['name']);

            if ($request->hasFile('image')) {
                $imagePath = $request
                    ->file('image')
                    ->store('products', 'public');
            }

            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,

                // OTOMATIS
                'category_id' => $bundlingCategory->id,
                'subcategory_id' => null,
            ]);

            // B. Buat Variant Bundle (stok = 0 karena dihitung dari komponen)
            $variant = $product->variants()->create([
                'name' => $validated['name'],
                'sku' => strtoupper($validated['bundle_sku']),
                'price' => $validated['bundle_price'],
                'stock' => 0,
            ]);

            // C. Buat Bundle Items
            $bundleData = [];
            foreach ($validated['components'] as $component) {
                $bundleData[] = [
                    'parent_variant_id' => $variant->id,

                    'component_variant_id' => $component['variant_id'],

                    'quantity' => $component['quantity'],

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ProductBundleItem::insert($bundleData);

            DB::commit();

            return back()->with('success', 'Paket bundling "'.$product->name.'" berhasil dibuat.');

        } catch (\Exception $e) {

            DB::rollBack();
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error(
                'Bundle Store Error: '.
                $e->getMessage()
            );

            return back()
                ->withErrors([
                    'system' => 'Terjadi kesalahan sistem saat membuat paket.',
                ])
                ->withInput();
        }
    }

    /**
     * Update Paket Bundling
     */
    public function updateBundle(Request $request, Product $product)
    {
        // Pastikan produk ini memang bundle
        $bundleVariant = $product->variants()->whereHas('bundleComponents')->first();
        abort_unless($bundleVariant, 403, 'Produk ini bukan paket bundling.');
        if ($this->hasPendingOrder($product)) {
            return back()->withErrors(['bundle' => 'Paket masih digunakan oleh pesanan yang menunggu pembayaran.']);
        }

        // Sanitization & Validation (sama seperti store)
        $requestData = $request->all();
        $cleanComponents = [];
        if (
            isset($requestData['components']) &&
            is_array($requestData['components'])
        ) {
            foreach ($requestData['components'] as $component) {
                if (
                    ! empty($component['variant_id']) &&
                    ! empty($component['quantity'])
                ) {
                    $cleanComponents[] = [
                        'variant_id' => (int) $component['variant_id'],
                        'quantity' => (int) $component['quantity'],
                    ];
                }
            }
        }
        $requestData['components'] = $cleanComponents;

        unset($requestData['category_id']);

        $request->replace($requestData);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'regex:/^[A-Za-z\s()-]+$/',
                Rule::unique('products', 'name')->ignore($product->id),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('products', 'slug')
                    ->ignore($product->id),
            ],

            'description' => 'nullable|string|max:1000',

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],

            'bundle_sku' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique(
                    'product_variants',
                    'sku'
                )->ignore($bundleVariant->id),
            ],

            'bundle_price' => 'required|integer|min:0',

            'components' => [
                'required',
                'array',
                'min:1',
            ],

            'components.*.variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'components.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'name.required' => 'Nama paket wajib diisi.',
            'name.max' => 'Nama paket tidak boleh lebih dari 150 karakter.',
            'name.regex' => 'Nama paket hanya boleh menggunakan huruf, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',
            'name.unique' => 'Nama sudah digunakan.',

            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda strip (-).',
            'slug.max' => 'Slug tidak boleh lebih dari 150 karakter.',
            'slug.unique' => 'Slug ini sudah digunakan oleh produk lain.',

            'description.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',

            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',

            'bundle_sku.required' => 'SKU paket wajib diisi.',
            'bundle_sku.max' => 'SKU paket tidak boleh lebih dari 50 karakter.',
            'bundle_sku.regex' => 'SKU hanya boleh menggunakan huruf besar, angka, dan tanda strip (-).',
            'bundle_sku.unique' => 'SKU paket sudah digunakan.',

            'bundle_price.required' => 'Harga paket wajib diisi.',
            'bundle_price.integer' => 'Harga paket harus berupa angka.',
            'bundle_price.min' => 'Harga paket tidak boleh negatif.',

            'components.required' => 'Paket wajib memiliki minimal 1 komponen.',
            'components.array' => 'Format komponen paket tidak valid.',
            'components.min' => 'Paket wajib memiliki minimal 1 komponen.',

            'components.*.variant_id.required' => 'Komponen wajib dipilih.',
            'components.*.variant_id.integer' => 'Komponen tidak valid.',
            'components.*.variant_id.exists' => 'Komponen yang dipilih tidak ditemukan.',

            'components.*.quantity.required' => 'Jumlah komponen wajib diisi.',
            'components.*.quantity.integer' => 'Jumlah komponen harus berupa angka.',
            'components.*.quantity.min' => 'Jumlah komponen minimal 1.',
        ]);

        $componentIds = collect($validated['components'])->pluck('variant_id');
        if ($componentIds->duplicates()->isNotEmpty()) {
            return back()
                ->withErrors([
                    'components' => 'Komponen tidak boleh diduplikasi.',
                ])
                ->withInput();
        }

        $invalidComponents = ProductVariant::whereIn(
            'id',
            $componentIds
        )
            ->whereHas('product.category', function ($query) {
                $query->where('name', '!=', 'Bahan');
            })
            ->exists();

        if ($invalidComponents) {
            return back()
                ->withErrors([
                    'components' => 'Komponen bundling hanya boleh berasal dari kategori Bahan.',
                ])
                ->withInput();
        }

        $nestedBundle = ProductVariant::whereIn(
            'id',
            $componentIds
        )
            ->whereHas('bundleComponents')
            ->exists();

        if ($nestedBundle) {
            return back()
                ->withErrors([
                    'components' => 'Komponen tidak boleh berupa paket bundling lain.',
                ])
                ->withInput();
        }

        $bundlingCategory = $this->getBundlingCategory();

        DB::beginTransaction();
        $oldImage = $product->image;
        $newImage = null;
        try {
            $imagePath = $product->image;

            if ($request->hasFile('image')) {
                $newImage = $request
                    ->file('image')
                    ->store('products', 'public');
                $imagePath = $newImage;
            }

            $product->update([
                'name' => $validated['name'],

                'slug' => $this->uniqueSlug($validated['slug'] ?? null, $validated['name'], $product->id),

                'description' => $validated['description'] ?? null,

                'image' => $imagePath,

                // Tetap Bundling
                'category_id' => $bundlingCategory->id,

                'subcategory_id' => null,
            ]);

            $bundleVariant->update([
                'name' => $validated['name'],
                'sku' => strtoupper(
                    $validated['bundle_sku']
                ),
                'price' => $validated['bundle_price'],

                // Selalu 0 karena bukan stok fisik.
                'stock' => 0,
            ]);

            // Sync Bundle Items
            $bundleVariant->bundleComponents()->delete();
            $bundleData = [];
            foreach ($validated['components'] as $component) {
                $bundleData[] = [
                    'parent_variant_id' => $bundleVariant->id,

                    'component_variant_id' => $component['variant_id'],

                    'quantity' => $component['quantity'],

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            ProductBundleItem::insert($bundleData);

            DB::commit();
            if ($newImage && $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            return back()->with('success', 'Paket bundling berhasil diperbarui.');

        } catch (\Exception $e) {

            DB::rollBack();
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            Log::error(
                'Bundle Update Error: '.
                    $e->getMessage()
            );

            return back()
                ->withErrors([
                    'system' => 'Terjadi kesalahan sistem saat memperbarui paket.',
                ])
                ->withInput();
        }
    }

    /**
     * Hapus Paket Bundling
     */
    public function destroyBundle(Product $product)
    {
        $bundleVariant = $product->variants()->whereHas('bundleComponents')->first();
        abort_unless($bundleVariant, 403, 'Produk ini bukan paket bundling.');
        if ($this->hasPendingOrder($product)) {
            return back()->withErrors(['delete' => 'Paket masih digunakan oleh pesanan yang menunggu pembayaran.']);
        }

        DB::beginTransaction();
        try {
            $image = $product->image;
            $product->delete(); // Cascade hapus variant & bundle items
            DB::commit();
            if ($image) {
                Storage::disk('public')->delete($image);
            }

            return back()->with('success', 'Paket bundling berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['system' => 'Gagal menghapus paket.']);
        }
    }

    private function uniqueSlug(?string $input, string $name, ?int $exceptId = null): string
    {
        $base = $input ?: Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (Product::where('slug', $slug)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists()) {
            $tail = '-'.$suffix++;
            $slug = substr($base, 0, 150 - strlen($tail)).$tail;
        }

        return $slug;
    }

    private function hasPendingOrder(Product $product): bool
    {
        return OrderItem::whereIn('product_variant_id', $product->variants()->select('id'))
            ->whereHas('order', fn ($query) => $query->where('status', 'pending_payment'))
            ->exists();
    }
}
