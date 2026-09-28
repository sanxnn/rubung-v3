<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'subcategory_id', 'name', 'slug', 'description', 'image'];

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function subcategory(): BelongsTo { return $this->belongsTo(Subcategory::class); }
    public function variants(): HasMany { return $this->hasMany(ProductVariant::class); }

    public function getLowestStockAttribute(): int
    {
        return $this->variants()->min('stock') ?? 0;
    }
}
