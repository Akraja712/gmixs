<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Staffs extends Model
{
    use Notifiable;

    protected $table = 'staffs';

    protected $fillable = [
        'name', 'mobile', 'password',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}

