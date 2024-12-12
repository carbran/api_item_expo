<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'status', // deve sempre ser informado e ele pode ser StatusEnum::ACTIVE ou StatusEnum::INACTIVE
        // mas ao consultar categorias só devem me voltar as que estão ativas
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_category', 'category_id', 'collection_id');
    }
}
