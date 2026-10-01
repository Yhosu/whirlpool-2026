<x-admin-layout>
    <div class="card">
        <div class="card-header">
            {{-- <div class="row">
                <div class="col-md-6"> --}}
                    <h4 class="card-title">about</h4>
                {{-- </div>
                <div class="col-md-6"> --}}
                    <a href="#" class="btn btn-icon btn-primary waves-effect waves-float waves-light">
                        <x-cy-icon-feather icon="plus" /> Crear
                    </a>
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
                    Filters
                
                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-2 align-self-end">
                        <button type="submit" class="btn btn-icon btn-primary waves-effect waves-float waves-light" title="{{ __('filter') }}">
                            <x-cy-icon-feather icon="filter" /> {{ __('table.search') }}
                        </button>
                    </div>
                </form> 
            </div>
            about PAGE LIST
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
        }],
        order: [1, 'asc']
    });

    $('#btn-show-all-doc').on('click', expandCollapseAll);

    function expandCollapseAll() {
        table.rows('.parent').nodes().to$().find('td:first-child').trigger('click').length || 
        table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click')
    }
    
</script>
