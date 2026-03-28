<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreCash extends Model
{
    public $timestamps   = false;
    protected $table     = 'store_cash';
    protected $fillable  = [
        'store_id', 'cash_date', 'opening_cash',
        'staff_confirmed', 'confirmed_by', 'confirmed_at',
    ];
    protected $casts = [
        'cash_date'       => 'date',
        'opening_cash'    => 'decimal:2',
        'staff_confirmed' => 'boolean',
        'confirmed_at'    => 'datetime',
    ];

    public function store()       { return $this->belongsTo(Store::class); }
    public function confirmedBy() { return $this->belongsTo(User::class, 'confirmed_by'); }
}