<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    public $timestamps   = false;
    protected $table     = 'staff_attendance';
    protected $fillable  = [
        'user_id', 'store_id', 'login_date',
        'login_time', 'ip_address',
    ];
    protected $casts = [
        'login_date' => 'date',
        'login_time' => 'string',
    ];

    public function user()  { return $this->belongsTo(User::class); }
    public function store() { return $this->belongsTo(Store::class); }
}