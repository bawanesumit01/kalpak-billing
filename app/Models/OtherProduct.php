<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OtherProduct extends Model
{
    public $timestamps   = false;
    protected $table     = 'other_product';
    protected $fillable  = [
        'store_id', 'product_code', 'name', 'category_id',
        'gst_rate', 'price', 'stock', 'is_deleted',
        'modified_by', 'modify',
    ];
    protected $casts = [
        'price'      => 'decimal:2',
        'gst_rate'   => 'decimal:2',
        'is_deleted' => 'boolean',
        'modify'     => 'datetime',
    ];

    // Scope: only non-deleted
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    public function store()     { return $this->belongsTo(Store::class); }
    public function modifiedBy(){ return $this->belongsTo(User::class, 'modified_by'); }
}