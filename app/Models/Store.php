<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public $timestamps  = false;
    protected $table    = 'stores';
    protected $fillable = ['name'];

    public function products()        { return $this->hasMany(Product::class); }
    public function otherProducts()   { return $this->hasMany(OtherProduct::class); }
    public function invoices()        { return $this->hasMany(Invoice::class); }
    public function staff()           { return $this->hasMany(User::class); }
    public function balanceFlows()    { return $this->hasMany(DailyBalanceFlow::class); }
    public function cashTransactions(){ return $this->hasMany(CashTransaction::class); }
}