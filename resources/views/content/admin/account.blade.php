<x-admin-layout>
    @push('prehead')
        {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/templates/admin/resources/css/vendors/pickers/pickadate/pickadate.css') }}"> --}}
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/templates/admin/resources/css/vendors/pickers/flatpickr/flatpickr.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/templates/admin/resources/css/others/forms/pickers/form-flat-pickr.css') }}">
        {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/templates/admin/resources/css/others/forms/pickers/form-pickadate.css') }}"> --}}
    @endpush

    <section class="app-user-view-account">
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <div class="card">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <img class="img-fluid rounded mt-3 mb-2" src="{{ asset('assets/img/avatar.svg') }}" height="110" width="110" alt="User avatar" />
                                <div class="user-info text-center">
                                    <h4>{{ auth()->user()->name }}</h4>
                                    <span class="badge bg-light-secondary">{{ __('administrator') }}</span>
                                </div>
                            </div>
                        </div>
                        <h4 class="fw-bolder border-bottom pb-50 mb-1">{{ __('details') }}</h4>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('name') }}:</span>
                                    <span>{{ auth()->user()->name }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('username') }}:</span>
                                    <span>{{  auth()->user()->username }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('email') }}:</span>
                                    <span>{{ auth()->user()->email }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('birthdate') }}:</span>
                                    <span>{{ auth()->user()->birthdate ? auth()->user()->birthdate : '-' }}</span>
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('state') }}:</span>
                                    @if(auth()->user()->active)
                                        <span class="badge bg-light-success">{{ __('active') }}</span>
                                    @else
                                        <span class="badge bg-light-danger">{{ __('inactive') }}</span>
                                    @endif
                                </li>
                                <li class="mb-75">
                                    <span class="fw-bolder me-25">{{ __('role') }}:</span>
                                    <span>{{ __('administrator') }}</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-center pt-2">
                                <a href="javascript:;" class="btn btn-primary me-1" data-bs-target="#editUser" data-bs-toggle="modal">{{ __('edit') }}</a>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <div class="card">
                    <div class="card-body">
                        <livewire:components.forms.user-security-edit />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <livewire:components.modals.user-edit />

    @push('scripts')
        {{-- <script src="{{ asset('assets/templates/admin/resources/js/vendors/pickers/pickadate/picker.js') }}"></script>
        <script src="{{ asset('assets/templates/admin/resources/js/vendors/pickers/pickadate/picker.date.js') }}"></script>
        <script src="{{ asset('assets/templates/admin/resources/js/vendors/pickers/pickadate/picker.time.js') }}"></script> --}}
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
                        // altInput: false,
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

