<div class="card-body">
    <h5 class="card-subtitle mb-1">{{ __('filters') }}</h5>
    <form method="POST" class="row gy-1" wire:submit.prevent="submit">
        @foreach ($fields as $key => $field)
            <x-capyei.field
                label="{{ $field->comment }}"
                type="{{ $field->type }}"
                name="{{ $field->name }}"
                propertyField='wire:model.defer="form.{{ $field->name }}"'
                id="{{ $field->name.'-'.$key }}"
                placeholder="{{ $field->placeholder ?? '' }}"
                required="{{ $field->required ?? false }}"
                :errors="$errors->get(''.$field->name.'')"
                class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2"
            />
        @endforeach

        <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2 align-self-end">
            <button type="submit" class="btn btn-icon btn-primary waves-effect waves-float waves-light" title="{{ __('filter') }}">
                <x-cy-icon-feather icon="filter" />
            </button>
        </div>
    </form>
</div>
