<div class="d-block">
    {{-- @if (session()->has('message_success'))
        <x-cy-alert type="success">
            {{ session('message_success') }}
        </x-cy-alert>
    @elseif(session()->has('message_error'))
        <x-cy-alert type="error">
            {{ session('message_error') }}
        </x-cy-alert>
    @endif --}}
    
    <form method="POST" class="row gy-1" action="{{ route('form', ['node' => $node]) }}" enctype="multipart/form-data">
        @csrf
        <input name="action" type="hidden" hidden value="{{ $action }}">
        <input name="node" type="hidden" hidden value="{{ $node }}">
        @if ($id)
            <input name="id" type="hidden" hidden value="{{ $id }}">
        @endif

        @foreach ($fields as $key => $field)
            @if ($field->type === 'image' || $field->type === 'video' || $field->type === 'file')
                <x-capyei.field
                    label="{{ $field->comment }}"
                    type="{{ $field->type }}"
                    name="{{ $field->name }}"
                    node="{{$node}}"
                    description="{{ $item->description ?? '' }}"
                    {{-- propertyField='wire:model.live="form.{{ $field->name }}"' --}}
                    id="{{ $field->name.'-'.$key }}"
                    placeholder="{{ $field->placeholder ?? '' }}"
                    :options="$field->options"
                    required="{{ $field->required ?? false }}"
                    :errors="$errors->get(''.$field->name.'')"
                    :item="$item"
                    class="col-12 {{ $field->type == 'froala' ? 'col-md-12' : 'col-md-6'}}"
                />
            @else
                <x-capyei.field
                    label="{{ $field->comment }}"
                    type="{{ $field->type }}"
                    name="{{ $field->name }}"
                    description="{{ $item->description ?? '' }}"
                    {{-- propertyField='wire:model.defer="form.{{ $field->name }}"' --}}
                    value="{{ old($field->name) ?? $item[$field->name] ?? null }}"
                    id="{{ $field->name }}"
                    placeholder="{{ $field->placeholder ?? '' }}"
                    :options="$field->options"
                    required="{{ $field->required ?? false }}"
                    :errors="$errors->get(''.$field->name.'')"
                    class="col-12 {{ $field->type == 'froala' ? 'col-md-12' : 'col-md-6'}}"
                />
            @endif
        @endforeach
    
        @if ($action !== 'read')
            <div class="col-12">
                <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">{{ __('table.actions.'.$action.'') }}</button>
            </div>
        @endif
    </form>
</div>

<script>
    let editor = new FroalaEditor(".froala-textarea", {
        events: { 
            'contentChanged': function () { 
                $(".froala-textarea").text(this.html.get())
                $(".froala-textarea").attr('value', this.html.get())
            }, 
        } 
    }, function(){
    });
</script>