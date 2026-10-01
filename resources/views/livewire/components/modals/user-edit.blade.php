<div wire:ignore.self class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-edit-user">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-5 px-sm-5 pt-50">
                <div class="text-center mb-2">
                    <h1 class="mb-1">{{ __('account_information') }}</h1>
                    <p>{{ __('account_information_description') }}</p>
                </div>
                <form id="editUserForm" wire:submit.prevent="updateProfileInformation" onsubmit="return false" class="row gy-1 pt-75">
                    {{-- <div class="col-12 col-md-6">
                        <label class="form-label" for="modalEditUserFirstName">{{ __('name') }}</label>
                        <input type="text" wire:model.defer="name" id="modalEditUserFirstName" name="name" class="form-control" placeholder="John" data-msg="Please enter your first name" />
                    </div> --}}

                    <x-capyei.field
                        type="text"
                        name="name"
                        propertyField='wire:model.defer="name"'
                        label="{{ __('name') }}"
                        id="modalEditUserFirstName"
                        placeholder="{{ __('enter_your').' '.strtolower(__('name')) }}"
                        required
                        :errors="$errors->get('name')"
                        class="col-12 col-md-6"
                    />

                    <x-capyei.field
                        type="text"
                        name="last_name"
                        propertyField='wire:model.defer="last_name"'
                        label="{{ __('last_name') }}"
                        id="modalEditUserLastName"
                        placeholder="{{ __('enter_your').' '.strtolower(__('last_name')) }}"
                        required
                        :errors="$errors->get('last_name')"
                        class="col-12 col-md-6"
                    />

                    <x-capyei.field
                        type="text"
                        name="username"
                        propertyField='wire:model.defer="username"'
                        label="{{ __('username') }}"
                        id="modalEditUserName"
                        placeholder="{{ __('enter_your').' '.strtolower(__('username')) }}"
                        required
                        :errors="$errors->get('username')"
                        class="col-12"
                    />

                    <x-capyei.field
                        type="email"
                        name="email"
                        propertyField='wire:model.defer="email"'
                        label="{{ __('email') }}"
                        id="modalEditUserEmail"
                        placeholder="{{ __('enter_your').' '.strtolower(__('email')) }}"
                        required
                        :errors="$errors->get('email')"
                        class="col-12 col-md-6"
                    />

                    <x-capyei.field
                        type="text"
                        name="birthdate"
                        property='wire:ignore'
                        propertyField='wire:model.defer="birthdate"'
                        label="{{ __('birthdate') }}"
                        id="modalEditUserBirthdate"
                        placeholder="{{ __('enter_your').' '.strtolower(__('birthdate')) }}"
                        required
                        :errors="$errors->get('birthdate')"
                        class="col-12 col-md-6"
                        classField="flatpickr-basic"
                    />

                    <x-capyei.field
                        type="switch"
                        name="active"
                        property='wire:ignore'
                        propertyField='wire:model.defer="active"'
                        label="{{ __('active_question') }}"
                        id="modalEditUserStatus"
                        placeholder="{{ __('enter_your').' '.strtolower(__('active_question')) }}"
                        :errors="$errors->get('active')"
                        class="col-12 col-md-6"
                    />

                    <div class="col-12 text-center mt-2 pt-50">
                        <button type="button" class="btn btn-outline-danger me-1" data-bs-dismiss="modal" aria-label="{{ __('cancel') }}">{{ __('cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>