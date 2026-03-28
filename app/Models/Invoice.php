<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public $timestamps   = false;
    protected $table     = 'invoices';
    protected $fillable  = [
        'invoice_no', 'store_id', 'customer_name', 'customer_phone',
        'payment_mode', 'subtotal', 'discount_total',
        'invoice_discount_amount', 'gst_total', 'total_amount','status',
        'created_by', 'created_at',
    ];
    protected $casts = [
        'created_at'               => 'datetime',
        'subtotal'                 => 'decimal:2',
        'discount_total'           => 'decimal:2',
        'invoice_discount_amount'  => 'decimal:2',
        'gst_total'                => 'decimal:2',
        'total_amount'             => 'decimal:2',
    ];

    public function store()    { return $this->belongsTo(Store::class); }
    public function items()    { return $this->hasMany(InvoiceItem::class); }
    public function createdBy(){ return $this->belongsTo(User::class, 'created_by'); }
}