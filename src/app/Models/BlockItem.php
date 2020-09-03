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
        'type', 
        'model', 
        'model_id', 
        'publish',
    ];

    // public function parent()
    // {
    //     return $this->belongsTo('Backpack\BlockCRUD\app\Models\BlockItem', 'parent_id');
    // }

    // public function children()
    // {
    //     return $this->hasMany('Backpack\BlockCRUD\app\Models\BlockItem', 'parent_id');
    // }

    // public function page()
    // {
    //     return $this->belongsTo('Backpack\PageManager\app\Models\Page', 'page_id');
    // }

    // public function url()
    // {
    //     switch ($this->type) {
    //         case 'external_link':
    //             return $this->link;
    //             break;

    //         case 'internal_link':
    //             return is_null($this->link) ? '#' : url($this->link);
    //             break;

    //         default: //page_link
    //             if ($this->page) {
    //                 return url($this->page->slug);
    //             }
    //             break;
    //     }
    // }
}
