<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public $timestamps   = false;
    protected $table     = 'users';
    protected $fillable  = [
        'username', 'password', 'full_name',
        'role', 'store_id',
    ];
    protected $hidden = ['password'];

    public function store()      { return $this->belongsTo(Store::class); }
    public function attendance() { return $this->hasMany(StaffAttendance::class); }
    public function invoices()   { return $this->hasMany(Invoice::class, 'created_by'); }
}