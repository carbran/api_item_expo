<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionCategory extends Model
{
    use HasFactory;

    protected $table = 'collection_category';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'collection_id',
        'category_id',
    ];

    public function collection() {
        return $this->belongsTo(Collection::class,'collection_id');
    }

    public function category() {
        return $this->belongsTo(Category::class,'category_id');
    }
}
