<?php

namespace Backpack\BlockCRUD\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class BlockItem extends Model
{
    use CrudTrait;

    protected $table = 'block_items';
    protected $fillable = [
        'name',
        'slug',
        'type',
        'content',
        'model', 
        'model_id', 
        'publish',
    ];

    public function scopeActive($query)
    {
        return $query->where('publish', 1);
    }
}
