<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'item';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'collection_id',
        'title',
        'subtitle',
        'author',
        'acquisition_date',
        'condition',
        'size',
        'size_type',
        'amount',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'condition'        => 'integer',
        'size_type'        => 'integer',
        'amount'           => 'integer',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }
}
