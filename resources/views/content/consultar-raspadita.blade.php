<x-master-layout>
    @section('title', 'El Ticket Dorado - Consulta')
    @push('styles')
        @vite(['resources/scss/main.scss'])
    @endpush
    <!-- content -->
    <!-- Botón Ir Atrás -->
    <a href="{{ url('/landing-ticket-dorado') }}" style="position: absolute; top: 0; left: 0; background-color: #ba0c14; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; font-size: 0.95rem; z-index: 1000; font-family: 'Outfit', sans-serif; transition: background 0.3s;">
        ← Ir Atrás
    </a>

    <div class="consultar-raspadita-wrapper" style="position: relative;">
        <div class="landing">
            <div class="image-container center">
                <img class="image-back" src="{{ asset('assets/img/6.png') }}" alt="main image">
                
                <form action="{{ url('/validate-coupon') }}" method="POST">
                    @csrf
                    
                    <div style="width: 250px; margin: 0 auto 25px auto;">
                        <img src="{{ asset('assets/consul/ticketdorado.jpeg') }}" alt="El Ticket Dorado" style="width: 100%; height: auto; border-radius: 8px; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
                    </div>
                    
                    @if (session('message_error'))
                        <div style="background-color: #ffeef0; color: #e31b23; padding: 12px; border-radius: 6px; border: 1px solid #f5c2c7; text-align: center; font-size: 0.9rem; font-weight: 600; margin-bottom: 5px;">
                            {{ session('message_error') }}
                        </div>
                    @endif
                    
                    <div class="form-control">
                        <label for="ci_number">Número de carnet</label>
                        <input type="text" id="ci_number" name="ci_number" required>
                    </div>
                    
                    <div class="form-control">
                        <label for="control_code">Código de control</label>
                        <input type="text" id="control_code" name="control_code" required>
                    </div>
                    
                    <button type="submit">Enviar formulario</button>
                </form>
            </div>
        </div>
    </div>
    <!-- content end -->
</x-master-layout>
