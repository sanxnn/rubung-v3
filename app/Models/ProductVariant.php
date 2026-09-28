<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'name', 'sku', 'price', 'stock'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function bundleComponents(): HasMany { return $this->hasMany(ProductBundleItem::class, 'parent_variant_id'); }
    public function bundleAsComponent(): HasMany { return $this->hasMany(ProductBundleItem::class, 'component_variant_id'); }

    public function getIsBundleAttribute(): bool { return $this->bundleComponents()->exists(); }

    public function getAvailableBundleStockAttribute()
    {
        if (!$this->bundleComponents()->exists()) {
            return 0;
        }

        $stocks = [];

        foreach ($this->bundleComponents()->with('componentVariant')->get() as $component) {
            $componentStock = $component->componentVariant->stock;
            $quantity = $component->quantity;

            if ($quantity <= 0) {
                return 0;
            }

            $stocks[] = intdiv(
                $componentStock,
                $quantity
            );
        }

        return empty($stocks)
            ? 0
            : min($stocks);
    }
}
