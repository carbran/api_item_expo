<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPicture extends Model
{
    use HasFactory;
    protected $table = 'item_picture';

    protected $primaryKey = 'id';

    protected $fillable = [
        'item_id',
        'image_data',
    ];

    public function items()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
