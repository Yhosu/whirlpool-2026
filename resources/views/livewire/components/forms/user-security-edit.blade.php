<div class="w-full">
    <h4 class="fw-bolder border-bottom pb-50 mb-1">{{ __('security') }}</h4>
    <form wire:submit="updatePassword" class="validate-form">
        <div class="row">
            <div class="col-12">
                <x-action-message class="me-3" on="password-updated" type="success">
                    {{ __('saved') }}
                </x-action-message>
            </div>
            <div class="col-12 mb-1">
                <label class="form-label" for="account-old-password">{{ __('current_password') }}</label>
                <div class="input-group form-password-toggle input-group-merge">
                    <input wire:model="current_password" type="password" class="form-control" id="account-old-password" name="current_password" placeholder="Enter current password" data-msg="Please current password" />
                    <div class="input-group-text cursor-pointer">
                        <i data-feather="eye"></i>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('current_password')" />
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6 mb-1">
                <label class="form-label" for="account-new-password">{{ __('new_password') }}</label>
                <div class="input-group form-password-toggle input-group-merge">
                    <input wire:model="password" type="password" id="account-new-password" name="password" class="form-control" placeholder="Enter new password" />
                    <div class="input-group-text cursor-pointer">
                        <i data-feather="eye"></i>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" />
            </div>
            <div class="col-12 col-sm-6 mb-1">
                <label class="form-label" for="account-retype-new-password">{{ __('confirm_password') }}</label>
                <div class="input-group form-password-toggle input-group-merge">
                    <input wire:model="password_confirmation" type="password" class="form-control" id="account-retype-new-password" name="password_confirmation" placeholder="Confirm your new password" />
                    <div class="input-group-text cursor-pointer"><i data-feather="eye"></i></div>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" />
            </div>
            <div class="col-12">
                <div class="d-flex justify-content-center pt-2">
                    <button type="submit" class="btn btn-primary me-1 mt-1">{{ __('edit') }}</button>
                </div>
            </div>
        </div>
    </form>
</div>