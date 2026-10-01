<?php

namespace App\Livewire\Components\CrudAdmin;

use Livewire\Component;

class TableFormCrud extends Component
{
    public $fields = [];

    public $node;

    public $form = [];

    public function mount($fields, $node)
    {
        $this->fields = $fields;
        $this->node = $node;
    }

    public function submit() {}

    public function updateForm()
    {
        $fields = $this->fields;
        foreach ($fields ?? [] as $field) {
            $this->form[$field->name] = null;
        }

        $this->fields = $fields;
    }

    public function render()
    {
        $this->updateForm();

        return view('livewire.components.crud-admin.table-form-crud', [
            'fields' => $this->fields,
            'node' => $this->node,
        ]);
    }
}
