<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DailyBalanceFlow extends Model
{
    public $timestamps   = false;
    protected $table     = 'daily_balance_flow';
    protected $fillable  = [
        'store_id', 'balance_date', 'opening_balance',
        'closing_balance', 'created_by',
    ];
    protected $casts = [
        'balance_date'    => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    public function store()    { return $this->belongsTo(Store::class); }
    public function createdBy(){ return $this->belongsTo(User::class, 'created_by'); }
}