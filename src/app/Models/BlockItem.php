<?php

namespace Backpack\BlockCRUD\app\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
//use Storage;

class BlockItem extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'block_items';
    protected $fillable = [
        'name',
        'slug',
        'type',
        'content',
        'html_content',
        'model', 
        'model_id', 
        'publish',
    ];
    protected $fakeColumns = [
        'code',
    ];
    protected $casts = [
        'html_content' => 'array',
    ];


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('publish', 1);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
    public function getCodePreviewAttribute()
    {
        if($this->type == 'template') {
            try {

                $parameters = $this->html_content;
                $parameters['edit_mode'] = true;

                return view($this->model_id, $parameters)->render();

            } catch (\Exception $e) {
                logger($e->getMessage());
            }
        }
        
        return $this->content;
    }
}
