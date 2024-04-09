<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BoardRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BoardCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BoardCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Board::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/board');
        CRUD::setEntityNameStrings('board', 'boards');
        $this->crud->addClause('where', 'user_id', '=', backpack_user()->id);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name'      => 'title',
            'label'     => 'Заголовок',
            'linkTo' => fn($entry, $related_key) => route('page.board.id.index', ['id' => $entry->id]),
        ]);

        $this->crud->addColumn([
            'name'      => 'project_id',
            'label'     => 'Проект',
            'entity'    => 'project',
            'attribute' => 'title'
        ]);

        //CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(BoardRequest::class);
       // CRUD::setFromDb(); // set fields from db columns.

        $this->crud->addField([
            'name'      => 'title',
            'label'     => 'Заголовок',
        ]);

        $this->crud->addField([
            'name'      => 'project_id',
            'label'     => 'Проект',
            'entity'    => 'project',
            'attribute' => 'title'
        ]);


        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
