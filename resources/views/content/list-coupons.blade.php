<x-master-layout>
    @push('styles')

    @endpush

    @if (Session::has('message_success'))
        <div class="section-title text-align_center">
            <h2>{{ Session::get('message_success') }}</h2>
        </div>
    @elseif(Session::has('message_error'))
        <div class="section-title text-align_center">
            <h2>{{ Session::get('message_error') }}</h2>
        </div>
    @endif
    <!-- content  -->
    <div class="content">
        <div class="container max patch">
            <a href="{{ url('form-raspadita') }}" class="back">
                <span>←</span> Ir Atrás
            </a>
           <!-- Contenedor del botón -->
            <div class="container-button">
                <!-- Mensaje flotante -->
                <div class="message">
                    Si ganaste un premio, contáctanos
                </div>
                <img onclick="window.open('https://api.whatsapp.com/send?phone=59177919909&text=Buenas%2C%20acabo%20de%20ganar%20un%20premio%20en%20la%20promoci%C3%B3n%20empresarial%20de%20dismac%20y%20me%20gustar%C3%ADa%20canjear%20mi%20premio', '_blank');" src="{{asset('assets/img/whatsapp.png')}}" alt="" class="whatsapp">
            </div>
        </div>
        <div class="container max">
            <div class="landing">
                <div class="container-flex">
                    <div class="image-container">
                        <img class="image-back" src="{{ asset('assets/img/6.png') }}" alt="main image">
                        <img class="logo-raspadita" src="{{ asset('assets/img/8.png') }}" alt="main image">
                        <h1>*Asegúrate de raspar bien la superficie marcada, te espera una sorpresa !!</h1>
                        <div class="cards-grid" id="cardsContainer"></div>
                        <div class="modal" id="modal">
                            <div class="modal-content" style="background-image: url({{ asset('assets/img/4.png') }})">
                                <div class="modal-title" id="title__modal">¡¡¡ Felicidades !!!</div>
                                <div class="modal-amount" id="modalAmount">$100</div>
                                <div class="modal-button-container">
                                    <button class="modal-button" id="close-modal">Aceptar</button>
                                    <a class="whatsapp-button" id="btn__whatsapp" style="padding-top: 15px;" target="_blank">
                                        <img src="{{ asset('assets/img/whatsapp.png') }}" alt="whatsapp">
                                        Contáctate
                                    </a>
                                    <div>
                                        <div class="modal-message">Código</div>
                                        <div class="modal-code" id="modalCode">XXXX-XXXX-XXXX</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content end  -->

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script>
            $( document ).ready(function() {
                const prizes = ['50 Bs', '100 Bs', '200 Bs', '500 Bs', '1000 Bs'];
                const NUM_CARDS = {{ count( $participantCoupons ) }};
                const items = {!! json_encode( $participantCoupons ) !!};
                const cardsData = {};

                function generateCode() {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    let code = '';
                    for (let i = 0; i < 12; i++) {
                        if (i > 0 && i % 4 === 0) code += '-';
                        code += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    return code;
                }

                function drawScratchLayer(canvas) {
                    const ctx = canvas.getContext('2d');
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width;
                    canvas.height = rect.height;

                    ctx.fillStyle = '#999999';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    ctx.fillStyle = '#777777';
                    ctx.font = 'bold 14px Arial';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    for (let i = -canvas.width; i < canvas.width * 2; i += 50) {
                        for (let j = -canvas.height; j < canvas.height * 2; j += 30) {
                            ctx.save();
                            ctx.translate(i, j);
                            ctx.rotate(-Math.PI / 6);
                            ctx.fillText('dismac', 0, 0);
                            ctx.restore();
                        }
                    }
                }

                function withAnimation( index, value ) {
                    return `
                        <div class="card">
                            <input type="checkbox" name="" id="" hidden>
                            <div class="card-number" style="color:white;" id="title__success-${ value['id'] }" hidden>✅ Cupón raspado</div>
                            <div class="card-header">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="logo">
                                <div class="card-number">Tarjeta ${index + 1}/${NUM_CARDS}</div>
                            </div>
                            <div style="padding-bottom:10px; color:white;" id="title__price-${ value['id'] }" hidden>${value?.coupon?.price?.name ?? 'SIN PREMIO'}</div>
                            <div class="prize-display">${value?.coupon?.price?.name ?? 'SIN PREMIO'}</div>
                            <div class="scratch-section">
                                <div class="code-display">
                                    <div class="code-value">${value['code'] ?? ''}</div>
                                </div>
                                <canvas class="scratch-layer" data-card-id="${value['id']}"></canvas>
                            </div>
                        </div>
                    `;
                }

                function withoutAnimation( index, value ) {
                    return `
                        <div class="card">
                            <input type="checkbox" name="" id="" hidden>
                            <div class="card-number" style="color:white;" id="title__success">✅ Cupón raspado</div>
                            <br>
                            <div class="card-header">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="logo">
                                <div class="card-number">Tarjeta ${index + 1}/${NUM_CARDS}</div>
                            </div>
                            <div style="padding-bottom:10px; color:white;">${value?.coupon?.price?.name ?? 'SIN PREMIO'}</div>
                            <div class="scratch-section">
                                <div class="code-display">
                                    <div class="code-value">${value['code'] ?? ''}</div>
                                </div>
                                <canvas class="scratch-layer"></canvas>
                            </div>
                        </div>
                    `;
                }

                function createCard(value, index) {
                    console.log(value);
                    const code  = value['code'] ?? '';
                    const prize = value.coupon?.price?.name ?? 'SIN PREMIO';
                    const isExchanged = value['status'] == 'exchanged' ? true : false;
                    return isExchanged
                        ? withoutAnimation( index, value )
                        : withAnimation( index, value );
                }

                $('#close-modal').click(function (e) {
                    e.preventDefault();
                    document.getElementById('modal').classList.remove('active');
                });

                function resetAllCards( items ) {
                    const container = document.getElementById('cardsContainer');
                    container.innerHTML = '';
                    items.forEach( function(value, i, array) {
                        container.innerHTML += createCard(value, i);
                    });
                    setupAllCanvases();
                }

                function setupAllCanvases() {
                    const canvases = document.querySelectorAll('.scratch-layer');
                    canvases.forEach((canvas) => {
                        const cardId = canvas.getAttribute('data-card-id');
                        if( cardId ) {
                            // Obtener datos de la tarjeta
                            const prizeDiv = canvas.closest('.card').querySelector('.prize-display');
                            const codeDiv = canvas.closest('.card').querySelector('.code-value');
                            const statusDiv = document.getElementById(`status-${cardId}`);

                            // const prize = prizeDiv.textContent;
                            const prize = prizeDiv.textContent;
                            const code = codeDiv.textContent;

                            cardsData[cardId] = {
                                canvas: canvas,
                                ctx: canvas.getContext('2d'),
                                code: code,
                                prize: prize,
                                isDrawing: false,
                                complete: false,
                                statusDiv: statusDiv
                            };

                            drawScratchLayer(canvas);

                            // Mouse events
                            canvas.addEventListener('mousedown', () => {
                                cardsData[cardId].isDrawing = true;
                            });

                            document.addEventListener('mousemove', (e) => {
                                if (cardsData[cardId].isDrawing && !cardsData[cardId].complete) {
                                    scratch(cardId, e.clientX, e.clientY);
                                }
                            });

                            document.addEventListener('mouseup', () => {
                                cardsData[cardId].isDrawing = false;
                            });

                            // Touch events
                            canvas.addEventListener('touchstart', (e) => {
                                cardsData[cardId].isDrawing = true;
                                scratch(cardId, e.touches[0].clientX, e.touches[0].clientY);
                            });

                            document.addEventListener('touchmove', (e) => {
                                if (cardsData[cardId].isDrawing && !cardsData[cardId].complete) {
                                    scratch(cardId, e.touches[0].clientX, e.touches[0].clientY);
                                }
                            });

                            document.addEventListener('touchend', () => {
                                cardsData[cardId].isDrawing = false;
                            });
                        }
                    });
                }

                function scratch(cardId, x  , y) {
                    const data = cardsData[cardId];
                    const rect = data.canvas.getBoundingClientRect();
                    const canvasX = x - rect.left;
                    const canvasY = y - rect.top;

                    const SCRATCH_RADIUS = 30; // <-- increase radius here

                    data.ctx.globalCompositeOperation = 'destination-out';
                    data.ctx.beginPath();
                    data.ctx.arc(canvasX, canvasY, SCRATCH_RADIUS, 0, Math.PI * 2);
                    data.ctx.fill();
                    data.ctx.globalCompositeOperation = 'source-over';

                    const imageData = data.ctx.getImageData(0, 0, data.canvas.width, data.canvas.height);
                    const pixels = imageData.data;
                    let transparent = 0;

                    for (let i = 3; i < pixels.length; i += 4) {
                        if (pixels[i] === 0) transparent++;
                    }

                    const percent = (transparent / (data.canvas.width * data.canvas.height * 4)) * 100;

                    if (percent > 16.944) {
                        data.complete = true;
                        data.canvas.style.pointerEvents = 'none';

                        $('#title__success-'+cardId).show();
                        $('#title__price-' + cardId).show();
                        $('#title__modal').text('¡¡¡ Felicidades !!!');
                        $('#btn__whatsapp').show();
                        if( data.prize == 'SIN PREMIO' ) {
                            $('#title__modal').text('¡¡¡ Lo sentimos, sigue participando !!!');
                            $('#btn__whatsapp').hide();
                        }

                        var txtButton = 'https://api.whatsapp.com/send?phone=59177919909&text=Buenas%2C%20acabo%20de%20ganar%20un%20premio%20en%20la%20promoci%C3%B3n%20empresarial%20de%20dismac%20y%20me%20gustar%C3%ADa%20canjear%20mi%20premio%2C%20mi%20cup%C3%B3n%20es%20el%20siguiente%20' + data.code;
                        $('#btn__whatsapp').attr('href', txtButton)

                        $.ajax({
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '/update-coupon',
                            data: { 'coupon': data.code },
                            success: function(data) {}
                        });

                        document.getElementById('modalAmount').textContent = data.prize;
                        document.getElementById('modalCode').textContent = data.code;
                        document.getElementById('modal').classList.add('active');
                    }
                }
                // Inicializar
                resetAllCards( items );
            })
        </script>
    @endpush
</x-master-layout>
