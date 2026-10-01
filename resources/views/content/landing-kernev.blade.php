<x-master-layout>
    @push('styles')

    @endpush

    <!-- content  -->
    <nav class="navbar">
        <div class="nav-left">
            <div class="logo">
                <img src="{{ asset('assets/img/logo-dismac.png') }}" alt="Dismac" class="logo-img">
            </div>
            <div class="nav-menu" id="nav-menu">
                <button class="close-menu-btn" onclick="toggleMenu()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <a href="{{ url('coupons') }}" class="nav-link">Ver cupones</a>
                <a href="{{ asset('assets/docs/bases.pdf') }}" target="_blank" class="nav-link">Ver bases legales</a>
            </div>
            <button class="hamburger-btn" id="hamburger" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-carousel" id="hero-carousel">
        <div class="carousel-slide hero-slide active" style="background-image: url('{{ asset('assets/hisense/hisense.jpg') }}');">
            <img src="{{ asset('assets/hisense/hisense.jpg') }}" alt="Hisense Promo" class="hero-banner">
        </div>
        <div class="carousel-slide hero-slide" style="background-image: url('{{ asset('assets/hisense/toshiba.jpg') }}');">
            <img src="{{ asset('assets/hisense/toshiba.jpg') }}" alt="Toshiba Promo" class="hero-banner">
        </div>
        
        <div class="carousel-indicators-container">
            <div class="carousel-indicator active" onclick="goToCarouselSlide(0)">
                <div class="carousel-progress"></div>
            </div>
            <div class="carousel-indicator" onclick="goToCarouselSlide(1)">
                <div class="carousel-progress"></div>
            </div>
        </div>
    </header>

    <!-- Prizes Header -->
    <div class="gradient-header prizes-header">
        <h2>¡Gana 2 Paquetes Dobles al partido Uruguay - España!</h2>
    </div>

    <!-- World Cup Banner Prize -->
    <div class="worldcup-prize-card">
        <div class="worldcup-prize-bg" style="background-image: url('{{ asset('assets/img/mundial_bg_2.png') }}');"></div>
        <div class="worldcup-prize-overlay"></div>
        <div class="worldcup-prize-content">
            <p class="prize-desc-worldcup">Sorteo aleatorio a través de una tómbola digital entre todos los cupones acumulados.</p>
            <p class="prize-desc-worldcup">El espectacular sorteo se realizará en vivo el <strong>2 de junio de 2026</strong> de manera presencial en la Tienda DISMAC en la ciudad de Santa Cruz ubicada en 5to Anillo NORTE– Av. Cristo Redentor #5180. Prepárate para vivir la fiesta más grande del mundo.</p>
            <div class="prize-tags-worldcup">
                <span class="tag-gold"><i class="fa-solid fa-ticket"></i> 2 Paquetes Dobles</span>
                <span class="tag-blur"><i class="fa-regular fa-futbol"></i> Partido Uruguay - España</span>
                <span class="tag-blur"><i class="fa-solid fa-globe"></i> Todo incluido</span>
            </div>
        </div>
    </div>

    <!-- Dynamic Process Flow -->
    <section class="process-flow-section">
        <div class="container">
            <h2 class="flow-title">Tu camino al Mundial en 2 pasos</h2>
            
            <div class="flow-container">
                <!-- Flow Step 1 -->
                <div class="flow-item">
                    <div class="flow-badge">Paso 1</div>
                    <div class="flow-content-wrapper">
                        <div class="flow-text-side">
                            <h3 class="flow-step-title">Compra y participa</h3>
                            <p class="flow-step-text">
                                <span class="md:whitespace-nowrap">Adquiere productos <strong>Hisense o Toshiba</strong> y por cada <strong class="whitespace-nowrap">Bs&nbsp;500</strong></span>
                                de compra recibe un cupón digital.
                            </p>
                            <div class="flow-highlight-box">
                                <i class="fa-solid fa-star hot-star"></i>
                                <p>¡Paga con <strong>MiniCuotas</strong> y duplica tus cupones!</p>
                            </div>
                        </div>
                        <div class="flow-visual-side">
                            <div class="brand-showcase">
                                <img src="{{ asset('assets/img/logo_hisense.png') }}" alt="Hisense" class="showcase-logo">
                                <div class="logos-divider"></div>
                                <img src="{{ asset('assets/img/logo_toshiba.png') }}" alt="Toshiba" class="showcase-logo">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Connecting Arrow -->
                <div class="flow-connector">
                    <div class="flow-connector-inner">
                        <i class="fa-solid fa-chevron-down mobile-only"></i>
                        <i class="fa-solid fa-chevron-right desktop-only"></i>
                    </div>
                </div>

                <!-- Flow Step 2 -->
                <div class="flow-item item-featured">
                    <div class="flow-badge">Paso 2</div>
                    <div class="flow-content-wrapper">
                        <div class="flow-visual-side">
                            <div class="full-display-image">
                                <img src="{{ asset('assets/img/paso2-kernel-2.jpg') }}" alt="Verifica tus cupones">
                            </div>
                        </div>
                        <div class="flow-text-side">
                            <h3 class="flow-step-title">Verifica tus cupones</h3>
                            <p class="flow-step-text">Tus cupones son digitales y se generan al instante. Valida tus datos y descubre cuántas oportunidades tienes de ganar.</p>
                            <a href="{{ url('coupons') }}" class="flow-cta-btn">
                                Consultar mis cupones <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Premium Redesigned Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <!-- Info Box with Blue Border -->
            <div class="footer-info-card">
                <div class="info-card-line">
                    <strong>Periodo de participación: </strong> 
                    <span>Para compras a partir desde la notificación de la Resolución de Autorización al 30 de mayo de 2026.</span>
                </div>
                <div class="info-card-line">
                    <strong>Promoción válida para: </strong> 
                    <span>Santa Cruz, La Paz, El Alto, Cochabamba, Sucre y Tarija.</span>
                </div>
                
            </div>

            <div class="footer-divider-line"></div>

            <!-- Legal Row: Logos & Text -->
            <div class="footer-legal-row">
                <div class="footer-logos-side">
                    <img src="{{ asset('assets/img/aj.jpeg') }}" alt="AJ" class="footer-brand-logo">
                </div>
                <div class="footer-legal-side">
                    <p>Actividad de promoción empresarial regulada y fiscalizada por la Autoridad de Juegos (AJ). Consulta los términos y condiciones completos en nuestros canales oficiales y sitio web: <a href="https://www.dismac.com.bo" target="_blank">www.dismac.com.bo</a></p>
                </div>
            </div>
        </div>
    </footer>

    {{-- <footer>
        <div class="footer-links">
            <a href="{{ asset('assets/docs/bases.pdf') }}" target="_blank" class="footer-link">Ver bases legales</a>
        </div>
        <div class="footer-socials">
            <a href="https://www.facebook.com/DismacBolivia?locale=es_LA" target="_blank" class="social-link"
                aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/dismacbolivia/" target="_blank" class="social-link"
                aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.tiktok.com/@dismac_bolivia" target="_blank" class="social-link" aria-label="TikTok"><i
                    class="fa-brands fa-tiktok"></i></a>
        </div>
        <p>&copy; 2025 Dismac. Todos los derechos reservados.</p>
    </footer> --}}

    <button id="back-to-top" class="back-to-top" title="Volver arriba">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    <!-- content end  -->

    @push('scripts')
    <script>
        let slideIndex = 0;
        let slideInterval;
        const defaultDuration = 5000;

        function showCarouselSlide(index) {
            const slides = document.querySelectorAll('.hero-slide');
            const indicators = document.querySelectorAll('.carousel-indicator');
            
            if (slides.length === 0) return;

            slides.forEach(s => s.classList.remove('active'));
            indicators.forEach(i => {
                i.classList.remove('active');
                void i.offsetWidth; // Force reflow
            });

            slideIndex = index;
            if (slideIndex >= slides.length) slideIndex = 0;
            if (slideIndex < 0) slideIndex = slides.length - 1;

            slides[slideIndex].classList.add('active');
            indicators[slideIndex].classList.add('active');

            resetInterval();
        }

        function goToCarouselSlide(index) {
            showCarouselSlide(index);
        }

        function resetInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(() => {
                showCarouselSlide(slideIndex + 1);
            }, defaultDuration);
        }

        function toggleMenu() {
            const menu = document.getElementById('nav-menu');
            menu.classList.toggle('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Carousel Init
            const firstIndicator = document.querySelector('.carousel-indicator.active');
            if(firstIndicator) {
                firstIndicator.classList.remove('active');
                void firstIndicator.offsetWidth;
                firstIndicator.classList.add('active');
            }
            resetInterval();
            
            // Back to top
            const backToTopBtn = document.getElementById('back-to-top');
            if (backToTopBtn) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 300) {
                        backToTopBtn.classList.add('show');
                    } else {
                        backToTopBtn.classList.remove('show');
                    }
                });
                backToTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }

            // Scroll animations
            const observerOptions = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.premium-step-card, .prizes-summary, .important-info-section').forEach(el => {
                el.classList.add('reveal-on-scroll');
                observer.observe(el);
            });
        });
    </script>
    @endpush
</x-master-layout>
