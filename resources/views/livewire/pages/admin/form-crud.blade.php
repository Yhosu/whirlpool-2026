<x-admin-layout>
    @push('prehead')
        {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/templates/admin/resources/css/vendors/pickers/pickadate/pickadate.css') }}"> --}}
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/templates/admin/resources/css/vendors/pickers/flatpickr/flatpickr.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/templates/admin/resources/css/others/forms/pickers/form-flat-pickr.css') }}">
        {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/templates/admin/resources/css/others/forms/pickers/form-pickadate.css') }}"> --}}
    @endpush

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('table.actions.'.$action) }} | <b>{{ $title }}</b></h4>
        </div>
        <div class="card-body">
            {{-- <livewire:components.crud-admin.form-crud 
                :title="$title"
                :fields="$fields"
                :item="$item"
                :message="$message"
                :id="$id"
                :action="$action"
                :node="$node"
            /> --}}

            @livewire('components.crud-admin.form-crud', [
                'title'   => $title,
                'fields'  => $fields,
                'item'    => $item,
                'message' => $message,
                'id'      => $id,
                'action'  => $action,
                'node'    => $node,
            ])
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('assets/templates/admin/resources/js/vendors/pickers/pickadate/legacy.js') }}"></script>
        <script src="{{ asset('assets/templates/admin/resources/js/vendors/pickers/flatpickr/flatpickr.min.js') }}"></script>
        <script src="{{ asset('assets/templates/admin/resources/js/vendors/pickers/flatpickr/flatpickr-es.js') }}"></script>
    
        <script>
            (function (window, document, $) {
                'use strict';
    
                /*******  Flatpickr  *****/
                var basicPickr = $('.flatpickr-basic');
    
                // Default
                if (basicPickr.length) {
                    basicPickr.flatpickr({
                        altInput: true,
                        altFormat: "F j, Y",

                        // enableTime: false,
                        // dateFormat: 'd-m-Y',
                        // dateFormat: 'n/j/Y',
                        locale: 'es',
                        static: true
                    });
                }
            })(window, document, jQuery);
        </script>

        <script>
            $('#editUser').on('hidden.bs.modal', function () {
                Livewire.dispatch('modalClosed');
            });
            document.addEventListener('DOMContentLoaded', function () {
                Livewire.on('profileUpdated', () => {
                    $('#editUser').modal('hide')
                });
            });
        </script>
    @endpush
</x-admin-layout>