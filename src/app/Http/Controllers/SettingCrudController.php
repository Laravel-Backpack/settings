<?php

namespace Backpack\Settings\app\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SettingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel("Backpack\Settings\app\Models\Setting");
        CRUD::setEntityNameStrings(trans('backpack::settings.setting_singular'), trans('backpack::settings.setting_plural'));
        CRUD::setRoute(backpack_url('setting'));
    }

    public function setupListOperation()
    {
        // only show settings which are marked as active
        CRUD::addClause('where', 'active', 1);

        // columns to show in the table view
        CRUD::setColumns([
            [
                'name'  => 'name',
                'label' => trans('backpack::settings.name'),
            ],
            [
                'name'  => 'value',
                'label' => trans('backpack::settings.value'),
            ],
            [
                'name'  => 'description',
                'label' => trans('backpack::settings.description'),
            ],
        ]);
    }

    public function setupUpdateOperation()
    {
        CRUD::addField([
            'name'       => 'name',
            'label'      => trans('backpack::settings.name'),
            'type'       => 'text',
            'attributes' => [
                'disabled' => 'disabled',
            ],
        ]);

        $data = json_decode(CRUD::getCurrentEntry()->field, true);
        // add support for relationship by using select2_from_array field
        // if this field is nor really an normal select2_from_array
        if ('select2_from_array' == $data['type'] && ! is_array($data['options'])) {
            $options = explode('|', $data['options']);
            // EX: App\\Models\\Tag or App\\Models\\Tag|name or  App\\Models\\Tag|name|id
            $model = $options[0];
            $attribute = $options[1] ?? 'name';
            $id = $options[2] ?? 'id';
            $data['options'] = (new $model())::all([$id, $attribute])->pluck($attribute, $id)->toArray();
            // also support multiple entries by casting the value field
            // add the cast
            $this->crud->getModel()->withCasts(['value' => 'array']);
            // remove the current entry to reloaded it and apply the cast
            $this->crud->entry = null;
        }

        CRUD::addField($data);
    }
}
