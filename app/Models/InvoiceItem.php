<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    public $timestamps   = false;
    protected $table     = 'invoice_items';
    protected $fillable  = [
        'invoice_id', 'product_id', 'product_code', 'product_name',
        'qty', 'unit_price', 'discount_percent', 'discount_amount',
        'taxable_amount', 'gst_amount', 'total_amount',
    ];
    protected $casts = [
        'unit_price'       => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'taxable_amount'   => 'decimal:2',
        'gst_amount'       => 'decimal:2',
        'total_amount'     => 'decimal:2',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function product() { return $this->belongsTo(Product::class); }
}