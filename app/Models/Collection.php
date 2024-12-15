<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $table = 'collection';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'collection_category', 'collection_id', 'category_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
