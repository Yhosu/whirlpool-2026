<x-master-layout>
    @section('title', 'El Ticket Dorado')
    @push('styles')
        @vite(['resources/scss/main.scss'])
    @endpush

    <!-- Header Banner -->
    <div style="width: 100%; background: #004d3e; position: relative;">
        <img src="{{ asset('assets/landing-ticket/APOYOS/HERO%20BANNER%20copia.png') }}" alt="Gana hasta 2000 Bs"
            style="width: 100%; height: auto; display: block;">
        <img src="{{ asset('assets/img/aj-consul.jpeg') }}"
            style="position: absolute; bottom: 0; right: 0; width: 60px; z-index: 50;" alt="AJ Consul Logo">
    </div>

    <!-- Centered Body Wrapper for Main Content and Footer -->
    <div style="width: 100%; display: flex; justify-content: center; background-color: #ffffff; padding: 0.5rem 0;">
        <!-- CONTENEDOR PRINCIPAL QUE PASA A WIDTH 100% EN MOBILE -->
        <div class="main-container-wrapper"
            style="display: flex; flex-direction: column; overflow: hidden; position: relative;">

            <!-- Top Light Green Section -->
            <div class="light-green-bg font-montserrat"
                style="padding: 2.5rem 1rem; text-align: center; position: relative;">

                <!-- Confetti Background Layer -->
                <div
                    style="position: absolute; top:0; left:0; width:100%; height:100%; pointer-events: none; opacity: 0.8; z-index: 0; display: flex; justify-content: space-between;">
                    <!-- Simulando algo de confeti decorativo si existen assets adicionales, o dejando espacio para ello -->
                </div>

                <div style="position: relative; z-index: 10;">
                    <p style="color: white; font-size: 1.1rem; font-weight: 500; margin-bottom: 0;">Por la compra de
                        cualquier producto</p>
                    <h2
                        style="color: white; font-size: 3rem; font-weight: 900; margin-bottom: 0px; letter-spacing: -2px; text-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                        Consul</h2>
                    <h1
                        style="color: #003622; font-family: 'Outfit'; font-weight: 900; font-size: 2.5rem; text-transform: uppercase; line-height: 1.1; margin-bottom: 1.5rem;">
                        EXIGE TU TICKET<br />DORADO
                    </h1>

                    <div style="display: flex; justify-content: center; margin-bottom: 2rem;">
                        <a href="https://www.dismac.com.bo/marcas/consul.html" target="_blank" class="red-btn-dash">
                            <span
                                style="background-color: #E31B23; color: #fff; font-weight: 800; font-size: 1.1rem; padding: 0.6rem 2.5rem; border-radius: 9999px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 5px 15px rgba(227,27,35,0.4);">
                                VER PRODUCTOS
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </span>
                        </a>
                    </div>

                    <div
                        style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; align-items: flex-start;">
                        <div
                            style="display: flex; flex-direction: column; align-items: center; gap: 10px; max-width: 250px;">
                            <a href="{{ url('consultar-raspadita') }}" class="red-btn-dash">
                                <span
                                    style="background-color: #E31B23; color: yellow; font-weight: 800; font-size: 1.1rem; padding: 0.6rem 2.5rem; border-radius: 9999px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 5px 15px rgba(227,27,35,0.5);">
                                    JUGAR RASPADITA
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                    </svg>
                                </span>
                            </a>
                            <p
                                style="color: white; font-size: 0.8rem; font-weight: 700; margin: 0; text-transform: uppercase; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                para compras en la página Web de Dismac
                            </p>
                        </div>

                        <div
                            style="display: flex; flex-direction: column; align-items: center; gap: 10px; max-width: 250px;">
                            <a href="{{ asset('assets/docs/puntos-consul.pdf') }}" class="red-btn-dash" target="_blank">
                                <span
                                    style="background-color: #2e4d22; color: #fff; font-weight: 800; font-size: 1.1rem; padding: 0.6rem 2.5rem; border-radius: 9999px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 5px 15px rgba(46,77,34,0.5);">
                                    VER PUNTOS DE CANJE
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </span>
                            </a>
                            <p
                                style="color: white; font-size: 0.8rem; font-weight: 700; margin: 0; text-transform: uppercase; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                para compras en tienda
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- The Horizontal Stripe -->
            <div class="golden-stripe"></div>

            <!-- Dark Green Section -->
            <div class="dark-green-bg font-montserrat"
                style="display: flex; flex-direction: column; position: relative; min-height: 1100px; overflow: hidden;">

                <!-- Tickets pegados al fondo derecho -->
                <div class="tickets-container"
                    style="position: absolute; top: 2rem; right: -15px; display: flex; flex-direction: column; gap: 3rem; align-items: flex-end; z-index: 10;">
                    @foreach (['2000 Bs', '1000 Bs', '500 Bs', '200 Bs', '100 Bs'] as $prize)
                        <!-- Ticket item -->
                        <div class="ticket-gold"
                            style="padding: 0.8rem 0.5rem; width: 230px; text-align: center; padding-right: 1.5rem;">
                            <p
                                style="font-size: 0.75rem; font-weight: 800; color: white; text-shadow: 0 1px 2px rgba(0,0,0,0.6); text-transform: uppercase; margin: 0; line-height: 1;">
                                Consul</p>
                            <p
                                style="font-size: 1.3rem; font-weight: 900; color: #402c00; line-height: 1; text-transform: uppercase; margin: 0.2rem 0;">
                                GANASTE</p>
                            <p style="font-size: 1.7rem; font-weight: 900; color: #402c00; line-height: 1; margin: 0;">
                                {{ $prize }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <!-- Text Left side -->
                <div class="left-text-container"
                    style="position: absolute; top: 0; left: 0; bottom: 0; width: 60%; display: flex; justify-content: center; align-items: center; z-index: 20;">
                    <h2 class="text-consul-light font-outfit"
                        style="font-weight: 900; font-size: clamp(3.5rem, 7vw, 5.5rem); line-height: 0.95; text-transform: uppercase; text-shadow: 0 5px 10px rgba(0,0,0,0.3); text-align: left;">
                        TODOS<br />
                        LOS<br />
                        TICKETS<br />
                        TIENEN<br />
                        PREMIOS
                    </h2>
                </div>

                <!-- AJ Logo Bottom Left -->
                <div class="aj-container"
                    style="position: absolute; bottom: 5rem; left: 3rem; z-index: 20; max-width: 380px;">
                    <img src="{{ asset('assets/landing-ticket/APOYOS/AJlogoimpreso.png') }}"
                        style="width: 300px; object-fit: contain; margin-bottom: 1rem;" alt="Autoridad de Juegos">

                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <img src="{{ asset('assets/img/aj-consul.jpeg') }}"
                            style="width: 50px; object-fit: contain; border-radius: 2px;" alt="AJ Consul Logo">
                        <div
                            style="color: white; font-size: 0.75rem; text-align: justify; line-height: 1.4; font-family: 'Open Sans', sans-serif;">
                            <p style="margin: 0; padding: 0;">
                                La promoción empresarial “El Ticket Dorado de Consul” se desarrollará desde la
                                notificación con la Resolución Administrativa de Autorización al 31 de mayo de 2026 o
                                hasta agotar stock (lo que suceda primero). Para mayor informacion revisa bases y
                                condiciones.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Podium & Appliances Bottom Right -->
                <div class="podium-container" style="position: absolute; bottom: -5px; right: 0; z-index: 15;">
                    <div class="podium-scaler" style="position: relative; width: 450px; height: 350px;">

                        <!-- Podium Image (Pegado a la derecha abajo) -->
                        <img src="{{ asset('assets/landing-ticket/APOYOS/vecteezy_elegant-white-plain-podium-2-stage-perfect-for-element_11034294.png') }}"
                            alt="Podium"
                            style="width: 100%; position: absolute; bottom: -20px; right: -20px; object-fit: contain; z-index: 10;">

                        <!-- Electrodomesticos colocados DE FORMA MUY AGRUPADA hacia la derecha -->
                        <img src="{{ asset('assets/landing-ticket/APOYOS/bl1197_black.png') }}" class="appliance"
                            style="height: 260px; bottom: 80px; right: 50px; z-index: 20;" alt="Licuadora" />

                        <img src="{{ asset('assets/landing-ticket/APOYOS/mk-17s32a2_1.png') }}" class="appliance"
                            style="height: 180px; bottom: 60px; right: 20px; z-index: 30;" alt="Hervidor" />

                        <img src="{{ asset('assets/landing-ticket/APOYOS/20132881.png') }}" class="appliance"
                            style="height: 140px; bottom: 40px; right: 190px; z-index: 40;" alt="Tupper" />

                        <img src="{{ asset('assets/landing-ticket/APOYOS/JARRA.png') }}" class="appliance"
                            style="height: 100px; bottom: 70px; right: 130px; z-index: 50;" alt="Jarra" />

                        <img src="{{ asset('assets/landing-ticket/APOYOS/b96801002.png') }}" class="appliance"
                            style="height: 120px; bottom: 45px; right: 5px; z-index: 60;" alt="Sandwichera" />

                    </div>
                </div>

            </div>

            <!-- Footer Action Section -->
            <div class="font-outfit" style="background-color: white; padding: 1rem 0 0; text-align: center;">
                <h3
                    style="font-size: 1.5rem; font-weight: 900; color: #1f2937; text-transform: uppercase; margin-bottom: 0.1rem;">
                    Plazo de la promoción</h3>
                <p
                    style="font-size: 0.9rem; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 0.5rem;">
                    Del 22 de Abril al 31 de Mayo</p>

                <div class="footer-action-links"
                    style="display: flex; flex-direction: row; justify-content: stretch; width: 100%; border-top: 1px solid #e5e7eb;">
                    <a href="{{ asset('assets/docs/bases-consul.pdf') }}" target="_blank"
                        style="flex: 1; background-color: #00c04b; color: white; text-decoration: none; font-weight: 800; font-size: 0.85rem; padding: 1rem 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.3rem; text-transform: uppercase; border-right: 1px solid white; transition: background 0.3s;"
                        onmouseover="this.style.backgroundColor='#00a13e'"
                        onmouseout="this.style.backgroundColor='#00c04b'">
                        <img src="{{ asset('assets/landing-ticket/APOYOS/BASES.png') }}"
                            style="height: 18px; filter: brightness(0) invert(1);" alt="Bases">
                        Bases y Condiciones
                    </a>
                    <!-- <a href="{{ url('/registra-tus-compras') }}"
                        style="flex: 1; background-color: #00c04b; color: white; text-decoration: none; font-weight: 800; font-size: 0.85rem; padding: 1rem 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.3rem; text-transform: uppercase; border-left: 1px solid white; transition: background 0.3s;"
                        onmouseover="this.style.backgroundColor='#00a13e'"
                        onmouseout="this.style.backgroundColor='#00c04b'">
                        <img src="{{ asset('assets/landing-ticket/APOYOS/CARRITO.png') }}"
                            style="height: 18px; filter: brightness(0) invert(1);" alt="Carrito">
                        Registra Tus Compras
                    </a> -->
                </div>
            </div>

        </div>
    </div>
</x-master-layout>