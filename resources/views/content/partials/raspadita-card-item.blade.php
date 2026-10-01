<div class="scratch-card-container-wrapper" style="width: 100%;">
    <div class="scratch-card-container golden-ticket"
        style="background-image: url('{{ asset('assets/landing-ticket/APOYOS/ticket-background.png') }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: center;">
        <div class="ticket-inner-border"></div>

        <div class="ticket-header">
            <div class="ticket-logo-consul">Consul</div>
            <div class="ticket-year">2026</div>
        </div>

        <div class="ticket-body">
            <p class="ticket-instruction">Raspa aquí y</p>
            <h3 class="ticket-main-title">Descubre tu Premio</h3>

            <div class="scratch-area" id="scratch-wrapper-{{ $form->id }}">
                <div class="hidden-code-container">
                    <div class="hidden-code" style="font-size: 0.7rem; opacity: 0.6;">{{ $form->code }}</div>
                    <div class="prize-name" id="prize-text-{{ $form->id }}" style="display: block;">
                        {{ $form->prize?->name ?? 'PREMIO SORPRESA' }}
                    </div>
                </div>

                @if(!$form->is_scratched)
                    <canvas class="scratch-canvas" data-form-id="{{ $form->id }}"></canvas>
                @endif
            </div>
        </div>

        <div class="ticket-footer">
            <div class="ticket-footer-left">
                <img src="{{ asset('assets/landing-ticket/APOYOS/logo-ajnegro.png') }}" alt="AJ"
                    class="ticket-aj-logo">
            </div>
            <div class="ticket-footer-right">
                <p>La promoción empresarial "El ticket dorado de Consul" se desarrollará desde la fecha
                    de aprobación al 31 de mayo de 2025 o hasta agotar stock, Promoción válida en las
                    ciudades de Santa Cruz, Montero, La Paz, El Alto, Cochabamba, Sucre y Tarija. <b>Más
                        información: www.dismac.com.bo/ticketdoradoconsul.html</b></p>
            </div>
        </div>
    </div>

    @if($form->is_scratched)
        <div
            style="margin-top: 10px; background-color: #d4edda; color: #155724; padding: 8px 12px; border-radius: 6px; font-weight: bold; font-size: 0.8rem; text-align: center;">
            ¡Ya raspaste este cupón!
        </div>
    @else
        <div
            style="margin-top: 10px; font-size: 0.8rem; color: #555; text-align: center; font-weight: 600;">
            *Raspa la superficie gris.
        </div>
    @endif
</div>
