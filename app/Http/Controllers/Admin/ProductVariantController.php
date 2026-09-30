<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductBundleItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductVariantController extends Controller
{
    /**
     * Menyimpan varian baru (PCS atau Paket)
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s()-]+$/',
                Rule::unique('product_variants', 'name')
                    ->where('product_id', $product->id),
            ],
            'sku' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('product_variants', 'sku'),
            ],
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ], [
            'name.regex' => 'Nama varian hanya boleh menggunakan huruf, angka, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',
            'name.unique' => 'Nama varian sudah digunakan.',
            'name.required' => 'Nama varian wajib diisi.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.regex' => 'SKU hanya boleh mengandung huruf besar, angka, dan tanda strip (-).',
            'sku.unique' => 'SKU ini sudah digunakan oleh varian lain.',
            'price.required' => 'Harga wajib diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
        ]);

        // Produk kategori Bundling tidak boleh membuat varian dari halaman ini.
        if ($product->category?->name === 'Bundling') {
            return back()
                ->withErrors([
                    'variant' => 'Varian paket bundling harus dibuat melalui halaman Paket Bundling.'
                ])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $product->variants()->create([
                'name' => $validated['name'],
                'sku' => strtoupper($validated['sku']),
                'price' => $validated['price'],
                'stock' => $validated['stock'],
            ]);

            DB::commit();

            return back()->with(
                'success',
                'Varian berhasil ditambahkan.'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Variant Store Error: ' . $e->getMessage());

            return back()
                ->withErrors([
                    'system' => 'Terjadi kesalahan sistem saat menyimpan varian.'
                ])
                ->withInput();
        }

    }

    /**
     * Memperbarui varian yang sudah ada
     *
     * @param Request $request
     * @param Product $product
     * @param ProductVariant $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        // Strict Ownership Check
        abort_if($variant->product_id !== $product->id, 403, 'Akses ditolak.');

        // Jangan izinkan edit varian bundle dari controller ini.
        if ($variant->bundleComponents()->exists()) {
            return back()
                ->withErrors([
                    'variant' => 'Varian paket bundling harus dikelola melalui halaman Paket Bundling.'
                ])
                ->withInput();
        }

        // Produk kategori Bundling tidak boleh dikelola sebagai varian biasa.
        if ($product->category?->name === 'Bundling') {
            return back()
                ->withErrors([
                    'variant' => 'Produk bundling harus dikelola melalui halaman Paket Bundling.'
                ])
                ->withInput();
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s()-]+$/',
                Rule::unique('product_variants', 'name')
                    ->where('product_id', $product->id)
                    ->ignore($variant->id),
            ],
            'sku' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('product_variants', 'sku')
                    ->ignore($variant->id),
            ],
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ], [
            'name.regex' => 'Nama varian hanya boleh menggunakan huruf, angka, spasi, tanda kurung ( ) dan tanda strip (-). Angka dan simbol lainnya tidak diperbolehkan.',
            'name.unique' => 'Nama varian sudah digunakan.',
            'name.required' => 'Nama varian wajib diisi.',
            'sku.required' => 'SKU wajib diisi.',
            'sku.regex' => 'SKU hanya boleh mengandung huruf besar, angka, dan tanda strip (-).',
            'sku.unique' => 'SKU ini sudah digunakan oleh varian lain.',
            'price.required' => 'Harga wajib diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
        ]);

        DB::beginTransaction();

        try {
            $variant->update([
                'name' => $validated['name'],
                'sku' => strtoupper($validated['sku']),
                'price' => $validated['price'],
                'stock' => $validated['stock'],
            ]);

            DB::commit();

            return back()->with(
                'success',
                'Varian berhasil diperbarui.'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Variant Update Error: ' . $e->getMessage());

            return back()
                ->withErrors([
                    'system' => 'Terjadi kesalahan sistem saat memperbarui varian.'
                ])
                ->withInput();
        }

    }

    /**
     * Menghapus varian
     *
     * @param Product $product
     * @param ProductVariant $variant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product, ProductVariant $variant)
    {
        abort_if($variant->product_id !== $product->id, 403, 'Akses ditolak.');

        // Varian ini sedang digunakan sebagai komponen bundle.
        if ($variant->bundleAsComponent()->exists()) {
            return back()->withErrors([
                'delete' =>
                    'Gagal menghapus varian. Varian ini masih menjadi komponen dalam paket bundling.'
            ]);
        }

        // Jangan hapus bundle lewat controller varian biasa.
        if ($variant->bundleComponents()->exists()) {
            return back()->withErrors([
                'delete' =>
                    'Paket bundling harus dihapus melalui halaman Paket Bundling.'
            ]);
        }

        DB::beginTransaction();

        try {
            $variant->delete();

            DB::commit();

            return back()->with(
                'success',
                'Varian berhasil dihapus.'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Variant Delete Error: ' . $e->getMessage());

            return back()->withErrors([
                'system' =>
                    'Gagal menghapus varian karena kendala database.'
            ]);
        }
    }
}
