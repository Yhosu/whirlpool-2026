<x-admin-layout>
    <div class="card">
        <div class="card-header">
            {{-- <div class="row">
                <div class="col-md-6"> --}}
                    <h4 class="card-title">{{ $title }}</h4>
                {{-- </div>
                <div class="col-md-6"> --}}                    
                    @if( config('nodes.action_nodes.' . $node_name . '.enable_create' ) )
                    <a href="{{ route('form-create', ['node' => $node_name, 'action' => 'create']) }}" class="btn btn-icon btn-primary waves-effect waves-float waves-light">
                        <x-cy-icon-feather icon="plus" /> Create
                    </a>
                    @endif 
                    @if( config('nodes.action_nodes.' . $node_name . '.excel' ) )
                    <a href="{{ empty(request()->all()) ? request()->fullUrl() . '?excel=1' : request()->fullUrl() . '&excel=1' }}" class="btn btn-icon btn-primary waves-effect waves-float waves-light">
                        <x-cy-icon-feather icon="download" /> Excel
                    </a>
                    @endif 
                    @if( $node_name == 'property' )
                    <a href="{{ url('admin/sync-properties') }}" class="btn btn-icon btn-primary waves-effect waves-float waves-light">
                        <x-cy-icon-feather icon="plus" /> SYNCHRONIZE PROPERTIES
                    </a>
                    @endif 
                {{-- </div>
            </div> --}}
        </div>
        <div class="d-block">
            {{-- @livewire('components.crud-admin.table-form-crud', [
                'fields'  => $filters,
                'node'    => $node_name,
            ]) --}}
            <div class="card-body">
                <h5 class="card-subtitle mb-1">{{ __('table.filters') }}</h5>
                <form method="GET" class="row gy-1" action="">
                    @foreach ($filters as $key => $field)
                        <x-capyei.field
                            label="{{ $field->comment }}"
                            type="{{ $field->type }}"
                            name="{{ $field->name }}"
                            id="{{ $field->name.'-'.$key }}"
                            placeholder="{{ $field->placeholder ?? '' }}"
                            required="{{ $field->required ?? false }}"
                            :options="$field->options"
                            :errors="$errors->get(''.$field->name.'')"
                            class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6"
                        />
                    @endforeach

                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2 align-self-end">
                        <button type="submit" class="btn btn-icon btn-primary waves-effect waves-float waves-light" title="{{ __('filter') }}">
                            <x-cy-icon-feather icon="filter" /> {{ __('table.search') }}
                        </button>
                    </div>
                </form>
            </div>
            @if ($items)
                <div class="table-responsive" style="padding: 25px;">
                    <div class="paginator px-2">
                        {{ $items->links('vendor.pagination.bootstrap-5') }}
                    </div>

                    <table class="table display nowrap" id="table-responsive___id">
                        <thead>
                            <tr>
                                <th></th>
                                @foreach ($fields as $head)
                                    <th>{{ !empty( $head->comment ) ? $head->comment : __('table.' . $head->name ) }}</th>
                                @endforeach
                                <th style="min-width: 150px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td></td>
                                    @foreach ($fields as $head)
                                        @if( $head->type == 'image' )
                                            <td style="overflow: hidden; max-width: 120ch; white-space: nowrap;"><a href="{{\Asset::get_image_path($node_name. '-' .$head->name, 'normal', $item[$head->name])}}" target="_blank">{{ $item[$head->name] }}</a></td>
                                        @elseif( substr($head->name, -3) == '_id' )
                                            <td style="overflow: hidden; max-width: 120ch; white-space: nowrap;">{{ \Func::getNameRelation( $filters, $head->name, $item[$head->name]) }}</td>
                                        @elseif( $head->type == 'boolean' )
                                            <td style="overflow: hidden; max-width: 120ch; white-space: nowrap;">{{ $item[$head->name] == 1 ? 'Si' : 'No' }}</td>
                                        @elseif( $head->type == 'select' )
                                            <td style="overflow: hidden; max-width: 120ch; white-space: nowrap;">{{ __('table.subjects.' . $item[$head->name]) }}</td>
                                        @else 
                                            <td style="overflow: hidden; max-width: 120ch; white-space: nowrap;">{{ $item[$head->name] }}</td>
                                        @endif
                                    @endforeach
                                    <td style="min-width: 150px">
                                        <div wire:ignore class="d-block">
                                            @if($node_name == 'participant-coupon' && in_array( $item['business_unit'], ['RET', 'ACE'])  )
                                            <a href="{{ route('form-crud', ['node' => $node_name, 'action' => 'edit', 'id' => $item['id']]) }}" class="btn btn-icon btn-flat-success waves-effect">
                                                <x-cy-icon-feather icon="edit" />
                                            </a>
                                            @endif
                                            
                                            {{-- <a href="{{ route('form-crud', ['node' => $node_name, 'action' => 'read', 'id' => $item['id']]) }}" class="btn btn-icon btn-flat-info waves-effect">
                                                <x-cy-icon-feather icon="eye" />
                                            </a> --}}
                                            {{-- <button onclick="removeItem('{{ $item['id'] }}', '{{ $node_name }}')" role="button" class="btn btn-icon btn-flat-danger waves-effect">
                                                <x-cy-icon-feather icon="trash" />
                                            </button> --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function removeItem(id, node_name) {
                if (id) {
                    Swal.fire({
                        title: 'Confirmación',
                        text: `¿Deseas eliminar el ítem con ID "${id}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `{{ route('form-crud', ['node' => '_NODE_', 'action' => 'delete', 'id' => '_ID_']) }}`
                                .replace('_NODE_', node_name)
                                .replace('_ID_', id);
                        }
                    });
                }
            }
        </script>
    @endpush
</x-admin-layout>
<script>
    var table = $('#table-responsive___id').DataTable({
        responsive: {
            details: {
                type: 'column'
            }
        },
        columnDefs: [{
            className: 'control',
            orderable: false,
            targets: 0
        }]
    });

    $('#btn-show-all-doc').on('click', expandCollapseAll);

    function expandCollapseAll() {
        table.rows('.parent').nodes().to$().find('td:first-child').trigger('click').length ||
        table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click')
    }

</script>
