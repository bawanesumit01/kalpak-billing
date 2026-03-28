<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    public $timestamps    = false;
    protected $table      = 'cash_transactions';
    protected $fillable   = [
        'store_id', 'transaction_date', 'transaction_type',
        'amount', 'description', 'transaction_category', 'created_by',
    ];
    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function store()    { return $this->belongsTo(Store::class); }
    public function createdBy(){ return $this->belongsTo(User::class, 'created_by'); }
}