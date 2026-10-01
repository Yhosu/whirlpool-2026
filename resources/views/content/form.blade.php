<x-master-layout>
    @push('styles')

    @endpush

    <!-- content  -->
    <div class="container">
        <div class="landing">
            <div class="image-container center" >
                <img class="image-back" src="{{ asset('assets/img/6.png') }}" alt="main image">
                <form action="{{ url('validate-coupon') }}" method="POST">
                    @csrf
                    <img class="logos logo-1" src="{{ asset('assets/img/1.png') }}" alt="main image">
                    {{-- <img class="logos logo-2" src="{{ asset('assets/img/2.png') }}" alt="main image"> --}}
                    <img src="{{ asset('assets/img/8.png') }}" alt="main image">
                    <div class="form-control">
                        <label for="ci-number">Número de carnet</label>
                        <input type="text" name="ci_number" required value="{{ old('ci_number') }}">
                    </div>
                    <div class="form-control">
                        <label for="control-code">Código de control</label>
                        <input type="text" name="control_code" required value="{{ old('control_code') }}">
                    </div>
                    <button type="submit">Enviar formulario</a>
                </form>
                {{-- <img class="main-text" src="{{ asset('assets/img/5.png') }}" alt="main image">
                <img class="logos logo-3" src="{{ asset('assets/img/3.png') }}" alt="main image"> --}}
            </div>
        </div>
    </div>
    <!-- content end  -->

    @push('scripts')

    @endpush
</x-master-layout>
