<x-master-layout>
    @push('styles')

    @endpush

    <!-- content  -->
    <nav class="navbar">
        <div class="nav-left">
            <div class="logo">
                <img src="{{ asset('assets/img/logo-dismac.png') }}" alt="Dismac" class="logo-img">
            </div>
            <div class="nav-menu">
                <a href="{{ asset('assets/docs/bases.pdf') }}" target="_blank" class="nav-link">Ver bases legales</a>
            </div>
        </div>
        <a href="{{ url('form-raspadita') }}" class="cta-button nav-cta">¡Jugar RASPADITA!</a>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <picture>
            <source media="(min-width: 768px)" srcset="{{ asset('assets/img/banner-aj.png') }}">
            <img src="{{ asset('assets/img/banner-aj-mob.jpg') }}" alt="Portada Raspadita Mágica" class="hero-banner">
        </picture>
    </header>

    <!-- Steps Section -->
    <section class="steps-container">

        <!-- Header: Store -->
        <h2 class="section-headline-red">Pasos para compras en tiendas</h2>

        <!-- Step 1 Store -->
        <div class="step-card two-columns">
            <div class="step-header">
                <span class="step-number">PASO 1</span>
            </div>
            <div class="step-content-grid">
                <div class="step-text-col">
                    <p>Por cada <span class="highlight">Bs.1.500</span> en compras al <span
                            class="highlight">contado</span> o en <span class="highlight">MiniCuotas</span> en cualquier
                        tienda Dismac recibes tu raspadita.</p>
                    <p style="margin-top: 1rem;">El cajero te entregará tus raspaditas según el siguiente cuadro:</p>
                </div>
                <div class="step-visual-col">
                    <div class="step-image-container">
                        <img src="{{ asset('assets/img/paso1.png') }}" alt="Cuadro de premios" class="step-illustration">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2 Store -->
        <div class="step-card two-columns">
            <div class="step-header">
                <span class="step-number">PASO 2</span>
            </div>
            <div class="step-content-grid">
                <div class="step-text-col">
                    <p class="highlight text-large">¡Raspa en el momento y descubre tu premio!</p>
                    <p style="margin-top: 1rem;">Si no ganaste, ¡puedes participar nuevamente en tu siguiente compra!
                    </p>
                </div>
                <div class="step-visual-col">
                    <div class="step-image-container">
                        <img src="{{ asset('assets/img/paso2.png')  }}" alt="Raspando tarjeta" class="step-illustration">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3 Store -->
        <div class="step-card two-columns">
            <div class="step-header">
                <span class="step-number">PASO 3</span>
            </div>
            <div class="step-content-grid">
                <div class="step-text-col">
                    <p class="highlight">Si tu raspadita sale premiada:</p>
                    <ul style="list-style: none; padding: 0; margin-top: 0.5rem;">
                        <li>• Registra tus datos en la planilla de la tienda.</li>
                        <li>• Te entregamos tu <span class="highlight">premio al instante.</span></li>
                    </ul>
                </div>
                <div class="step-visual-col">
                    <div class="step-image-container">
                        <img src="{{ asset('assets/img/paso3.png') }}" alt="Entrega de premio" class="step-illustration">
                    </div>
                </div>
            </div>
        </div>

        <!-- Header: Online -->
        <h2 class="section-headline-red">Pasos para compras Online</h2>

        <!-- Step 1 Online -->
        <div class="step-card two-columns">
            <div class="step-header">
                <span class="step-number">PASO 1</span>
            </div>
            <div class="step-content-grid">
                <div class="step-text-col">
                    <p>Por cada <span class="highlight">Bs.1.500</span> en compras al <span
                            class="highlight">contado</span> o en <span class="highlight">MiniCuotas</span> en
                        la página
                        web <span class="highlight">dismac.com.bo</span> recibes tu raspadita digital.</p>
                </div>
                <div class="step-visual-col">
                    <div class="step-image-container">
                        <img src="{{ asset('assets/img/paso1.png') }}" alt="Cuadro de premios" class="step-illustration">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2 Online -->
        <div class="step-card two-columns">
            <div class="step-header">
                <span class="step-number">PASO 2</span>
            </div>
            <div class="step-content-grid">
                <div class="step-text-col">
                    <p>Te enviaremos un <span class="highlight">enlace único</span> por WhatsApp o email donde podrás
                        acceder a tu <span class="highlight">raspadita digital</span> y rasparla desde tu celular o
                        computadora.</p>
                </div>
                <div class="step-visual-col">
                    <div class="step-image-container">
                        <img src="{{ asset('assets/img/paso2.png') }}" alt="Raspadita Digital" class="step-illustration">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3 Online -->
        <div class="step-card two-columns" id="jugar">
            <div class="step-header">
                <span class="step-number">PASO 3</span>
            </div>
            <div class="step-content-grid">
                <div class="step-text-col">
                    <p class="highlight">¡Ingresa al enlace y prueba tu suerte!</p>
                    <p style="margin-top:1rem">Si ganas, podrás canjear tu premio en cualquiera de nuestras sucursales.
                    </p>
                </div>
                <div class="step-visual-col">
                    <div class="step-image-container">
                        <img src="{{ asset('assets/img/paso3.png') }}" alt="Canje de premio" class="step-illustration">
                    </div>
                </div>
            </div>
            <div class="pulse-cta-container">
                <a href="{{ url('form-raspadita')}}" class="cta-button pulse-button">¡Jugar
                    RASPADITA!</a>
            </div>
        </div>
    </section>

    <!-- Important Info Section -->
    <section class="important-info-section">
        <h2 class="important-title">Importante</h2>
        <ul class="important-list">
            <li>CI del cliente debe coincidir con el CI/NIT de la factura.</li>
            <li>Debes ser mayor a 18 años.</li>
            <li>EL premio no incluye envío a domicilio.</li>
            <li class="important-highlight">Campaña válida del 5 al 31 de diciembre o hasta agotar stock.</li>
        </ul>
    </section>

    <!-- Legal Info Section -->
    <section class="legal-info-section">
        <div class="legal-content">
            <a href="{{ asset('assets/docs/bases.pdf') }}" target="_blank" class="legal-link-large">Ver bases
                legales</a>

            <h4 class="legal-date-title">Periodo de Duración</h4>
            <p class="legal-dates">
                La promoción empresarial La raspadita mágica de Dismac se desarrollará desde la
                fecha de aprobación al 31 de diciembre de 2025 o hasta agotar stock. Promoción
                válida en las ciudades de Santa Cruz, Montero,
                La Paz, Cochabamba, Sucre y Tarija.
                Más información: www.dismac.com.bo
            </p>

            <div class="aj-logo-container">
                <img src="{{ asset('assets/img/aj.jpg') }}" alt="Autoridad de Juegos" class="aj-logo">
                <p class="legal-caption">Actividad Autorizada y Fiscalizada por la Autoridad de Juegos</p>
            </div>
        </div>
    </section>

    {{-- <footer>
        <div class="footer-links">
            <a href="#" class="footer-link">Inicio</a>
            <a href="{{ asset('assets/docs/bases.pdf') }}" target="_blank" class="footer-link">Ver bases legales</a>
        </div>
        <p>&copy; 2025 Dismac. Todos los derechos reservados.</p>
    </footer> --}}

    <button id="back-to-top" class="back-to-top" title="Volver arriba">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    <!-- content end  -->

    @push('scripts')
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            slides[index].classList.add('active');
            dots[index].classList.add('active');
        }

        function changeSlide(direction) {
            currentSlide += direction;
            if (currentSlide >= slides.length) {
                currentSlide = 0;
            } else if (currentSlide < 0) {
                currentSlide = slides.length - 1;
            }
            showSlide(currentSlide);
        }

        function goToSlide(index) {
            currentSlide = index;
            showSlide(currentSlide);
        }

        function autoPlayCarousel() {
            changeSlide(1);
        }

        setInterval(autoPlayCarousel, 5000);
    </script>
    @endpush
</x-master-layout>
