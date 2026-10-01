<x-master-layout>
    @push('styles')
    <style>
        :root {
            --primary-red: #e31b23;
            --dark-red: #991b1b;
            --gold-primary: #d4af37;
            --gold-light: #fef08a;
        }
        .mundial-section {
            background-color: #fdfefe;
            padding: 4rem 1.5rem;
            font-family: 'Outfit', sans-serif;
        }
        .mundial-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        /* Hero/Intro Info Box */
        .promo-intro-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3.5rem 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            margin-bottom: 4rem;
            text-align: center;
            color: white;
            box-sizing: border-box;
        }
        .promo-intro-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 80% 20%, rgba(227, 27, 35, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }
        .promo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(227, 27, 35, 0.15);
            border: 1px solid rgba(227, 27, 35, 0.3);
            color: #fca5a5;
            padding: 0.6rem 1.4rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .promo-title {
            font-size: 2.8rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
        }
        .promo-subtitle {
            font-size: 1.2rem;
            color: #94a3b8;
            max-width: 800px;
            margin: 0 auto 2.5rem;
            line-height: 1.6;
        }
        .combo-pill {
            background: linear-gradient(135deg, #e31b23 0%, #b91c1c 100%);
            border-radius: 20px;
            padding: 1.5rem 2.5rem;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            font-weight: 800;
            font-size: 1.4rem;
            color: white;
            box-shadow: 0 15px 30px rgba(227, 27, 35, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: transform 0.3s ease;
            max-width: 100%;
            box-sizing: border-box;
        }
        .combo-pill:hover {
            transform: scale(1.03);
        }
        .combo-pill span.highlight {
            color: #fef08a;
            font-size: 1.6rem;
            text-shadow: 0 2px 10px rgba(234, 179, 8, 0.3);
        }
        .combo-pill span.sub-text {
            font-size: 0.95rem;
            font-weight: 500;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Section Title Styles */
        .m-title-container {
            text-align: center;
            margin-bottom: 3.5rem;
        }
        .m-section-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
        }
        .m-section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: #e31b23;
            margin: 0.75rem auto 0;
            border-radius: 2px;
        }
        .m-section-desc {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0.5rem auto 0;
        }

        /* Steps Grid */
        .steps-timeline-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
            margin-bottom: 5rem;
        }
        .step-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 3rem 2rem 2.5rem;
            position: relative;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .step-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }
        .step-number {
            position: absolute;
            top: -24px;
            left: 28px;
            background: linear-gradient(135deg, #e31b23 0%, #b91c1c 100%);
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.3rem;
            box-shadow: 0 8px 20px rgba(227, 27, 35, 0.3);
        }
        .step-header {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0.5rem 0 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .step-header i {
            color: #e31b23;
            font-size: 1.3rem;
        }
        .step-body-desc {
            color: #475569;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        .step-body-desc strong {
            color: #0f172a;
        }
        .step-footer-info {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 1rem;
            font-size: 0.9rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .step-footer-info i {
            color: #ea580c;
            font-size: 1.1rem;
        }

        /* Brands Section */
        .brands-box {
            background: linear-gradient(to bottom, #f8fafc, #f1f5f9);
            border-radius: 28px;
            padding: 4rem 2rem;
            margin-bottom: 5rem;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .brands-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .brands-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            justify-content: center;
            align-items: center;
            max-width: 1000px;
            margin: 0 auto;
        }
        .brand-item {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1rem 2rem;
            font-weight: 800;
            color: #334155;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            min-width: 130px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-item:hover {
            background: #e31b23;
            color: white;
            border-color: #e31b23;
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 10px 20px rgba(227, 27, 35, 0.2);
        }

        /* Products Grid */
        .products-combo-container {
            margin-bottom: 5rem;
        }
        .products-layout-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .product-card {
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 20px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            transition: all 0.3s ease;
        }
        .product-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 25px rgba(15, 23, 42, 0.06);
        }
        .product-num-badge {
            background: #fef2f2;
            color: #e31b23;
            font-weight: 900;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            border: 1px solid #fee2e2;
        }
        .product-details {
            flex: 1;
        }
        .product-name {
            margin: 0 0 0.2rem 0;
            font-weight: 800;
            color: #0f172a;
            font-size: 1.05rem;
            line-height: 1.3;
        }
        .product-spec {
            margin: 0;
            color: #64748b;
            font-size: 0.85rem;
        }

        /* Draws Timeline */
        .draws-timeline-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 28px;
            padding: 4.5rem 2rem;
            margin-bottom: 5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .draws-timeline-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 10% 90%, rgba(227, 27, 35, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        .draws-main-title {
            font-size: 2.2rem;
            font-weight: 900;
            color: white;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }
        .draws-sub-title {
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto 3.5rem;
            font-size: 1.1rem;
        }
        .draws-flex-flow {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            gap: 1.5rem;
            max-width: 1000px;
            margin: 0 auto;
        }
        .draws-flex-flow::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 10%;
            right: 10%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1;
        }
        .draw-node {
            flex: 1;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .draw-bubble {
            background: #1e293b;
            border: 4px solid #0f172a;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: white;
            margin-bottom: 1.2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-size: 1.1rem;
            border-color: rgba(255, 255, 255, 0.1);
        }
        .draw-node:hover .draw-bubble {
            transform: scale(1.15);
            background: #e31b23;
            border-color: #fde047;
            box-shadow: 0 0 25px rgba(227, 27, 35, 0.65);
        }
        .draw-node.active .draw-bubble {
            background: #e31b23;
            border-color: #fde047;
            box-shadow: 0 0 20px rgba(227, 27, 35, 0.5);
        }
        .draw-info-box h5 {
            margin: 0 0 0.35rem 0;
            font-weight: 800;
            font-size: 1.15rem;
            color: white;
        }
        .draw-info-box p {
            margin: 0;
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 900px) {
            .draws-flex-flow {
                flex-direction: column;
                align-items: flex-start;
                gap: 2.5rem;
                padding-left: 2.5rem;
            }
            .draws-flex-flow::before {
                top: 0;
                bottom: 0;
                left: 48px;
                width: 4px;
                height: auto;
            }
            .draw-node {
                flex-direction: row;
                gap: 1.5rem;
                text-align: left;
                align-items: center;
            }
            .draw-bubble {
                margin-bottom: 0;
            }
        }

        @media (max-width: 768px) {
            .mundial-section {
                padding: 2.5rem 1rem;
            }
            .promo-intro-card {
                padding: 2.5rem 1.25rem;
                border-radius: 20px;
                margin-bottom: 2.5rem;
            }
            .promo-subtitle {
                font-size: 1rem;
                margin-bottom: 1.75rem;
            }
            .combo-pill {
                padding: 1.25rem 1.5rem;
                border-radius: 16px;
                gap: 0.4rem;
                width: 100%;
                display: flex;
            }
            .combo-pill span.highlight {
                font-size: 1.15rem;
                text-align: center;
                line-height: 1.3;
            }
            .combo-pill span.sub-text {
                font-size: 0.8rem;
                text-align: center;
                line-height: 1.4;
            }
            .m-title-container {
                margin-bottom: 2.5rem;
            }
            .m-section-title {
                font-size: 1.75rem;
            }
            .m-section-desc {
                font-size: 0.95rem;
            }
            .steps-timeline-grid {
                gap: 2rem;
                margin-bottom: 3.5rem;
            }
            .step-card {
                padding: 2.5rem 1.5rem 2rem;
                border-radius: 20px;
            }
            .step-header {
                font-size: 1.2rem;
            }
            .step-body-desc {
                font-size: 0.95rem;
                margin-bottom: 1.5rem;
            }
            .brands-box {
                padding: 2.5rem 1.25rem;
                border-radius: 20px;
                margin-bottom: 3.5rem;
            }
            .brands-title {
                font-size: 1.2rem;
                margin-bottom: 1.5rem;
            }
            .brands-flex {
                gap: 0.75rem;
            }
            .brand-item {
                padding: 0.75rem 1.25rem;
                font-size: 0.95rem;
                min-width: 100px;
                border-radius: 12px;
            }
            .products-combo-container {
                margin-bottom: 3.5rem;
            }
            .products-layout-grid {
                gap: 1rem;
            }
            .product-card {
                padding: 1rem;
                gap: 1rem;
                border-radius: 16px;
            }
            .product-name {
                font-size: 0.95rem;
            }
            .product-spec {
                font-size: 0.8rem;
            }
            .product-num-badge {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                font-size: 0.95rem;
            }
            .draws-timeline-section {
                padding: 3rem 1.25rem;
                border-radius: 20px;
                margin-bottom: 3.5rem;
            }
            .draws-main-title {
                font-size: 1.75rem;
            }
            .draws-sub-title {
                font-size: 0.95rem;
                margin-bottom: 2rem;
            }
        }
    </style>
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
                <a href="{{ url('coupons-mundial') }}" class="nav-link">Ver cupones</a>
                <a href="{{ asset('assets/docs/el-mundial-se-vive-en-casa.pdf') }}" target="_blank" class="nav-link">Ver bases legales</a>
            </div>
            <button class="hamburger-btn" id="hamburger" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-carousel" id="hero-carousel">
        <div class="carousel-slide hero-slide active" style="background-image: url('{{ asset('assets/img/el-mundial-se-vive-en-casa.png') }}');">
            <img src="{{ asset('assets/img/el-mundial-se-vive-en-casa.png') }}" alt="Hisense Promo" class="hero-banner">
        </div>
    </header>

    <!-- SECCIÓN PRINCIPAL DE LA PROMOCIÓN -->
    <section class="mundial-section">
        <div class="mundial-container">

            <!-- Card de Presentación / Hero de la Campaña -->
            <div class="promo-intro-card reveal-on-scroll">
                <span class="promo-badge">
                    <i class="fa-solid fa-trophy"></i> El mundial se vive en casa
                </span>
                
                <p class="promo-subtitle">
                    ¡La fiesta del fútbol está aquí! En Dismac queremos que disfrutes de la copa más grande del mundo como un verdadero rey. Prepárate para vivir los partidos en alta definición y con la mejor tecnología.
                </p>
                <div class="combo-pill">
                    <span class="sub-text">¡Cada ganador se lleva un combo completo de!</span>
                    <span class="highlight"><i class="fa-solid fa-gift"></i> 10 Electrodomésticos Espectaculares</span>
                    <span class="sub-text">Son 5 sorteos en total. ¡5 ganadores a nivel nacional!</span>
                </div>
            </div>

            <!-- Mecánica de Participación (Pasos) -->
            <div class="m-title-container reveal-on-scroll">
                <h2 class="m-section-title">¿Cómo participar?</h2>
                <p class="m-section-desc">Participar es súper fácil y rápido. Sigue estos pasos para acumular tus cupones digitales.</p>
            </div>

            <div class="steps-timeline-grid">
                <!-- Paso 1 -->
                <div class="step-card reveal-on-scroll">
                    <div>
                        <div class="step-number">1</div>
                        <h3 class="step-header"><i class="fa-solid fa-cart-shopping"></i> Compra y Acumula</h3>
                        <p class="step-body-desc">
                            Por cada <strong>Bs 500</strong> de compra en cualquiera de nuestras sucursales físicas a nivel nacional o a través de nuestra tienda online oficial <strong>www.dismac.com.bo</strong>, recibirás automáticamente <strong>1 cupón digital</strong>. ¡A mayor compra, más oportunidades de ganar!
                        </p>
                    </div>
                    <div class="step-footer-info">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Ej: Compra Bs 1,000 = Recibe 2 cupones.</span>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div class="step-card reveal-on-scroll">
                    <div>
                        <div class="step-number">2</div>
                        <h3 class="step-header"><i class="fa-solid fa-circle-up"></i> Duplica tus Oportunidades</h3>
                        <p class="step-body-desc">
                            ¿Quieres duplicar tus posibilidades? Si en tu compra adquieres productos de nuestras <strong>marcas auspiciadoras</strong> seleccionadas, ¡duplicarás la cantidad de cupones digitales generados al instante!
                        </p>
                    </div>
                    <div class="step-footer-info">
                        <i class="fa-solid fa-star"></i>
                        <span>¡Compra marcas auspiciadoras y multiplica por 2!</span>
                    </div>
                </div>

                <!-- Paso 3 -->
                <div class="step-card reveal-on-scroll">
                    <div>
                        <div class="step-number">3</div>
                        <h3 class="step-header"><i class="fa-solid fa-credit-card"></i> Premia tu Fidelidad</h3>
                        <p class="step-body-desc">
                            Si eres cliente con una línea de crédito <strong>MiniCuotas</strong> con saldo vigente y te encuentras al día en tus pagos al cierre de los meses de mayo y/o junio, ¡generarás automáticamente <strong>1 cupón digital mensual</strong> adicional!
                        </p>
                    </div>
                    <div class="step-footer-info">
                        <i class="fa-solid fa-percent"></i>
                        <span>MiniCuotas premia tu puntualidad mensualmente.</span>
                    </div>
                </div>
            </div>

            <!-- Marcas Auspiciadoras -->
            <div class="brands-box reveal-on-scroll">
                <h3 class="brands-title">Marcas Auspiciadoras que Duplican tus Cupones</h3>
                <div class="brands-flex">
                    <span class="brand-item">CONSUL</span>
                    <span class="brand-item">WHIRLPOOL</span>
                    <span class="brand-item">SONY</span>
                    <span class="brand-item">SAMSUNG</span>
                    <span class="brand-item">LG</span>
                    <span class="brand-item">OSTER</span>
                    <span class="brand-item">MAXI KING</span>
                    <span class="brand-item">KERNIG</span>
                    <span class="brand-item">JVC</span>
                </div>
            </div>

            <!-- El Combo de Premios (Los 10 Productos) -->
            <div class="products-combo-container">
                <div class="m-title-container reveal-on-scroll">
                    <h2 class="m-section-title">¿Qué incluye el Combo Mundialista?</h2>
                    <p class="m-section-desc">Cada uno de los 5 ganadores se llevará a casa los siguientes 10 productos espectaculares:</p>
                </div>

                <div class="products-layout-grid reveal-on-scroll">
                    <!-- 1 -->
                    <div class="product-card">
                        <div class="product-num-badge">01</div>
                        <div class="product-details">
                            <h4 class="product-name">Televisor Kernig 75" LED 4K</h4>
                            <p class="product-spec">UHD Smart TV Whale OS</p>
                        </div>
                    </div>
                    <!-- 2 -->
                    <div class="product-card">
                        <div class="product-num-badge">02</div>
                        <div class="product-details">
                            <h4 class="product-name">Torre de Sonido LG Xboom</h4>
                            <p class="product-spec">Sonido RNC9 de alta fidelidad</p>
                        </div>
                    </div>
                    <!-- 3 -->
                    <div class="product-card">
                        <div class="product-num-badge">03</div>
                        <div class="product-details">
                            <h4 class="product-name">Refrigerador Consul</h4>
                            <p class="product-spec">Single Door de 275 Litros</p>
                        </div>
                    </div>
                    <!-- 4 -->
                    <div class="product-card">
                        <div class="product-num-badge">04</div>
                        <div class="product-details">
                            <h4 class="product-name">Caja China "The Black" by Otto</h4>
                            <p class="product-spec">Caja china pequeña de 12 Kg</p>
                        </div>
                    </div>
                    <!-- 5 -->
                    <div class="product-card">
                        <div class="product-num-badge">05</div>
                        <div class="product-details">
                            <h4 class="product-name">Lavadora Whirlpool Carga Superior</h4>
                            <p class="product-spec">Capacidad de 10 Kg, color blanco</p>
                        </div>
                    </div>
                    <!-- 6 -->
                    <div class="product-card">
                        <div class="product-num-badge">06</div>
                        <div class="product-details">
                            <h4 class="product-name">Barra de Sonido LG Soundbar</h4>
                            <p class="product-spec">Potencia de 300W S40T</p>
                        </div>
                    </div>
                    <!-- 7 -->
                    <div class="product-card">
                        <div class="product-num-badge">07</div>
                        <div class="product-details">
                            <h4 class="product-name">Cocina Clásica Kernig</h4>
                            <p class="product-spec">Cocina de 5 hornallas en color blanco</p>
                        </div>
                    </div>
                    <!-- 8 -->
                    <div class="product-card">
                        <div class="product-num-badge">08</div>
                        <div class="product-details">
                            <h4 class="product-name">Freezer Wilson de 99 Litros</h4>
                            <p class="product-spec">Congelador de 1 puerta de alta eficiencia</p>
                        </div>
                    </div>
                    <!-- 9 -->
                    <div class="product-card">
                        <div class="product-num-badge">09</div>
                        <div class="product-details">
                            <h4 class="product-name">Cafetera a Goteo Jata</h4>
                            <p class="product-spec">Capacidad de 1 Litro con jarra termo</p>
                        </div>
                    </div>
                    <!-- 10 -->
                    <div class="product-card">
                        <div class="product-num-badge">10</div>
                        <div class="product-details">
                            <h4 class="product-name">Licuadora Jata 1300W</h4>
                            <p class="product-spec">2 velocidades y jarra de vidrio de 1.5 Lts</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cronograma de Sorteos -->
            <div class="draws-timeline-section reveal-on-scroll">
                <h3 class="draws-main-title">Cronograma de Sorteos Oficiales</h3>
                <p class="draws-sub-title">Los sorteos se realizarán en acto público de manera presencial en la Tienda DISMAC en la ciudad de Santa Cruz (5to Anillo NORTE – Av. Cristo Redentor #5180) bajo supervisión y fiscalización de la Autoridad de Juegos (AJ).</p>
                <div class="draws-flex-flow">
                    <!-- Sorteo 1 -->
                    <div class="draw-node active">
                        <div class="draw-bubble">1</div>
                        <div class="draw-info-box">
                            <h5>12 de Junio</h5>
                            <p>Primer Ganador</p>
                        </div>
                    </div>
                    <!-- Sorteo 2 -->
                    <div class="draw-node active">
                        <div class="draw-bubble">2</div>
                        <div class="draw-info-box">
                            <h5>19 de Junio</h5>
                            <p>Segundo Ganador</p>
                        </div>
                    </div>
                    <!-- Sorteo 3 -->
                    <div class="draw-node active">
                        <div class="draw-bubble">3</div>
                        <div class="draw-info-box">
                            <h5>26 de Junio</h5>
                            <p>Tercer Ganador</p>
                        </div>
                    </div>
                    <!-- Sorteo 4 -->
                    <div class="draw-node active">
                        <div class="draw-bubble">4</div>
                        <div class="draw-info-box">
                            <h5>3 de Julio</h5>
                            <p>Cuarto Ganador</p>
                        </div>
                    </div>
                    <!-- Sorteo 5 -->
                    <div class="draw-node active">
                        <div class="draw-bubble">5</div>
                        <div class="draw-info-box">
                            <h5>10 de Julio</h5>
                            <p>Quinto Ganador</p>
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
                    <span>La promoción se desarrollará desde la aprobación de la resolución al 10 de julio de 2026.</span>
                </div>
                <div class="info-card-line">
                    <strong>Promoción válida para: </strong> 
                    <span>Nivel Nacional.</span>
                </div>
                
            </div>

            <div class="footer-divider-line"></div>

            <!-- Legal Row: Logos & Text -->
            <div class="footer-legal-row">
                <div class="footer-logos-side">
                    <img src="{{ asset('assets/img/aj-junio.jpeg') }}" alt="AJ" class="footer-brand-logo">
                </div>
                <div class="footer-legal-side">
                    <p>Actividad de promoción empresarial regulada y fiscalizada por la Autoridad de Juegos (AJ). Consulta los términos y condiciones completos en nuestros canales oficiales y sitio web: <a href="https://www.dismac.com.bo" target="_blank">www.dismac.com.bo</a></p>
                </div>
            </div>
        </div>
    </footer>

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

            document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
    @endpush
</x-master-layout>
