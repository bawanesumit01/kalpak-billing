<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps   = false;
    protected $table     = 'products';
    protected $fillable  = [
        'store_id', 'product_code', 'name',
        'category_id', 'gst_rate', 'price', 'stock',
    ];
    protected $casts = [
        'price'    => 'decimal:2',
        'gst_rate' => 'decimal:2',
    ];

    public function store() { return $this->belongsTo(Store::class); }
}