<?php

namespace Backpack\BlockCRUD\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class BlockItemCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ReorderOperation;

    public function setup()
    {
        CRUD::setModel("Backpack\BlockCRUD\app\Models\BlockItem");
        CRUD::setRoute(config('backpack.base.route_prefix') . '/blocks');
        CRUD::setEntityNameStrings('block item', 'block items');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Название',
        ]);
        CRUD::addColumn([
            'label' => 'Тип',
            'name' => 'type',
            'type' => 'select_from_array',
            'options' => [
                'html' => 'HTML', 
                //'model' => 'Published (visible)'
            ],
        ]);

        CRUD::addColumn([
            'label' => 'Активен',
            'name' => 'publish',
            'type' => 'boolean',
        ]);

        // $this->crud->addButtonFromModelFunction('line', 'open', 'getOpenButton', 'beginning');
    }

    // -----------------------------------------------
    // Overwrites of CrudController
    // -----------------------------------------------

    protected function setupCreateOperation()
    {
        CRUD::addField([
            'name' => 'name',
            'label' => 'Название',
        ]);
        CRUD::addField([
            'name' => 'slug',
            'label' => 'Обозначение',
            'hint' => 'Латинские буквы, без пробелов',
        ]);
        CRUD::addField([
            'label' => 'Тип',
            'name' => 'type',
            'type' => 'select_from_array',
            'options' => [
                'html' => 'HTML', 
                //'model' => 'Published (visible)'
            ],
        ]);
        CRUD::addField([
            'name' => 'content',
            'label' => 'Содержание',
            'type' => 'edit_template',
            'view_namespace' => 'blockcrud::templates',
        ]);
        CRUD::addField([
            'label' => 'Активен',
            'name' => 'publish',
            'type' => 'checkbox',
            'default' => 1,
        ]);

        // $this->crud->setValidation(BlockRequest::class);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
