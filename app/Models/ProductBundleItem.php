<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBundleItem extends Model
{
    protected $fillable = ['parent_variant_id', 'component_variant_id', 'quantity'];
    public function parentVariant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'parent_variant_id'); }
    public function componentVariant(): BelongsTo { return $this->belongsTo(ProductVariant::class, 'component_variant_id'); }
}
