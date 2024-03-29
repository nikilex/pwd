<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PasswordRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PasswordCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PasswordCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Password::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/password');
        CRUD::setEntityNameStrings('пароль', 'пароли');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumns([
            [
                'name'      => 'title',
                'label'     => 'Заголовок',
            ],
            [
                'name'      => 'username',
                'label'     => 'Имя пользователя',
            ],
            [
                'name'  => 'password',
                'label' => 'Пароль',
                'type'  => 'text'
            ],
            [
                'name'  => 'description',
                'label' => 'Описание',
                'type'  => 'textarea'
            ]
        ]);
    }

        /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PasswordRequest::class);
        //CRUD::setFromDb(); // set fields from db columns.

        $this->crud->addField([
            'name'      => 'title',
            'label'     => 'Заголовок',
        ]);

        $this->crud->addField([
            'name'      => 'username',
            'label'     => 'Имя пользователя',
        ]);

        $this->crud->addField([
            'name'  => 'password',
            'label' => 'Пароль',
            'type'  => 'text'
        ]);

        $this->crud->addField([
            'name'  => 'description',
            'label' => 'Описание',
            'type'  => 'textarea'
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
