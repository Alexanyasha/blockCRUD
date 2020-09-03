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
                'html' => 'Настраиваемый самостоятельно', 
                //'model' => 'Published (visible)'
            ],
        ]);

        CRUD::addColumn([
            'label' => 'Активен',
            'name' => 'publish',
            'type' => 'checkbox',
            'checked' => true,
        ]);

        // $this->crud->addButtonFromModelFunction('line', 'open', 'getOpenButton', 'beginning');
    }

    // -----------------------------------------------
    // Overwrites of CrudController
    // -----------------------------------------------

    protected function setupCreateOperation()
    {
        // Note:
        // - default fields, that all templates are using, are set using $this->addDefaultPageFields();
        // - template-specific fields are set per-template, in the PageTemplates trait;

        // $this->addDefaultPageFields(request()->input('template'));
        // $this->useTemplate(request()->input('template'));

        // $this->crud->setValidation(PageRequest::class);
    }

    protected function setupUpdateOperation()
    {
        // if the template in the GET parameter is missing, figure it out from the db
        // $template = request()->input('template') ?? $this->crud->getCurrentEntry()->template;

        // $this->addDefaultPageFields($template);
        // $this->useTemplate($template);

        // $this->crud->setValidation(PageRequest::class);
    }
}
