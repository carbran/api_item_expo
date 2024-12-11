<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessCode extends Model
{
    use HasFactory;

    protected $table = 'access_code';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'access_code',
        'expires_in',
    ];

    protected $casts = [
        'expires_in' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isExpired()
    {
        return now()->greaterThan($this->expires_in);
    }
}
