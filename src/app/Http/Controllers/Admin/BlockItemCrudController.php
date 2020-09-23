<?php

namespace Backpack\BlockCRUD\app\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\BlockCRUD\app\Http\Requests\BlockRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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


    private function getModels(){
        $paths = [
            //app_path() => 'App',
            app_path() . '/Models' => 'App\Models',
        ];

        $out = [];

        foreach($paths as $path => $namespace) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                /**
                 * @var \SplFileInfo $item
                 */
                if($item->isReadable() && $item->isFile() && mb_strtolower($item->getExtension()) === 'php') {
                    $model = str_replace('/', '', mb_substr($item->getRealPath(), mb_strlen($path), -4));
                    $modelname = $model;
                    $entity_path = $namespace . '\\' . $model;

                    $entity = new $entity_path;
                    if(! $entity || isset($entity->blockcrud_ignore)) {
                        continue;
                    }

                    if($entity && isset($entity->blockcrud_title)) {
                        $modelname = $entity->blockcrud_title;
                    }

                    $out[$entity_path] =  $modelname;
                }
            }
        }

        return $out;
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
                'model' => 'Сущность',
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
        CRUD::setValidation(BlockRequest::class);
        
        CRUD::addField([
            'name' => 'name',
            'label' => 'Название',
            'allow_null' => false,
        ]);
        CRUD::addField([
            'name' => 'slug',
            'label' => 'Обозначение',
            'hint' => 'Латинские буквы, без пробелов',
            'allow_null' => false,
        ]);
        CRUD::addField([
            'label' => 'Тип',
            'name' => 'type',
            'type' => 'select_from_array',
            'options' => [
                'html' => 'HTML', 
                'model' => 'Сущность',
            ],
            'allow_null' => false,
        ]);

        $models_list = $this->getModels();

        if(count($models_list) > 0) {
            CRUD::addField([
                'label' => 'Сущность',
                'name' => 'model',
                'type' => 'toggle_template',
                'view_namespace' => 'blockcrud::templates',
                'options' => $models_list,
                'show_when' => [
                    'type' => 'model',
                ],
            ]);
        }

        CRUD::addField([
            'name' => 'content',
            'label' => 'Содержание',
            'type' => 'edit_template',
            'view_namespace' => 'blockcrud::templates',
            'show_when' => [
                'type' => 'html',
            ],
        ]);

        CRUD::addField([
            'label' => 'Активен',
            'name' => 'publish',
            'type' => 'checkbox',
            'default' => 1,
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation(false);
    }
}
