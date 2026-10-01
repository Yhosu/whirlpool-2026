<?php

namespace App\Livewire\Components\CrudAdmin;

use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormCrud extends Component
{
    use WithFileUploads;

    public $title;

    public $fields = [];

    public $item;

    public $message;

    public $id;

    public $action;

    public $node;

    public $description;

    public $form = [];

    public function mount($title, $fields, $item, $message, $id, $action, $node)
    {
        $this->title = $title;
        $this->fields = $fields;
        $this->item = $item;
        $this->message = $message;
        $this->id = $id;
        $this->action = $action;
        $this->node = $node;
    }

    public function submit()
    {
        \Log::info('Form data 1:', $this->form);

        if ($this->action !== 'read') {
            $this->form['node'] = $this->node;
            $this->form['action'] = $this->action;

            if ($this->id) {
                $this->form['id'] = $this->id;
            }
            if (isset($this->form['description']) && $this->description) {
                $this->form['description'] = $this->description;
            }

            $request = new Request($this->form);

            $result = app('App\Http\Controllers\ProcessController')->postNodeAction($request);
            if ($result['status']) {
                session()->flash('message_success', $result['message']);
                if (isset($result['item']) && $result['item']) {
                    $this->item = $result['item'];
                    $this->updateForm();
                }
            } else {
                if (isset($result['errors']) && is_array($result['errors']) && ! empty($result['errors'])) {
                    $this->dispatch('alertErrors', $result);
                } else {
                    session()->flash('message_error', ($result['message'] ?? trans('responses.error.default')));
                }
            }
        }
    }

    public function updateForm()
    {
        $fields = $this->fields;
        foreach ($fields ?? [] as $field) {
            if ($field->type !== 'password' && $field->type !== 'image' && $field->type !== 'video' && $field->type !== 'file') {
                $this->form[$field->name] = $this->item[$field->name] ?? null;
            } else {
                if (! isset($this->form[$field->name])) {
                    $this->form[$field->name] = null;
                }
            }
        }

        $this->fields = $fields;
    }

    public function render()
    {
        $this->updateForm();

        return view('livewire.components.crud-admin.form-crud', [
            'title' => $this->title,
            'fields' => $this->fields,
            'item' => $this->item,
            'message' => $this->message,
            'id' => $this->id,
            'action' => $this->action,
            'node' => $this->node,
        ]);
    }
}
