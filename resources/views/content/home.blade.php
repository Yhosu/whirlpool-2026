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
        <div class="container max">
            <div class="landing">
                <div class="container-flex">
                    <div class="image-container">
                        <img class="image-back" src="{{ asset('assets/img/6.png') }}" alt="main image">
                        <img class="logo-raspadita" src="{{ asset('assets/img/8.png') }}" alt="main image">
                        {{-- <h1>Raspa y gana</h1> --}}
                        <div class="cards-grid" id="cardsContainer"></div>
                        <div class="modal" id="modal">
                            <div class="modal-content">
                                <div class="modal-title"> &ensp; &ensp; ¡Felicidades Ganaste! 🎉</div>
                                <div class="modal-amount" id="modalAmount">$100</div>
                                <div class="modal-button-container">
                                    <button class="modal-button" id="close-modal">Aceptar</button>
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
                const NUM_CARDS = 5;
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

                function createCard(index) {
                    const code = generateCode();
                    const prize = prizes[Math.floor(Math.random() * prizes.length)];

                    return `
                        <div class="card">
                            <input type="checkbox" name="" id="" hidden>
                            <div class="card-header">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="logo">
                                <div class="card-number">Tarjeta ${index + 1}/${NUM_CARDS}</div>
                            </div>
                            <div class="prize-display">${prize}</div>
                            <div class="scratch-section">
                                <div class="code-display">
                                    <div class="code-value">${code}</div>
                                </div>
                                <canvas class="scratch-layer" data-card-id="${index}"></canvas>
                            </div>
                        </div>
                    `;
                }

                $('#close-modal').click(function (e) {
                    e.preventDefault();
                    document.getElementById('modal').classList.remove('active');
                });

                function resetAllCards() {
                    const container = document.getElementById('cardsContainer');
                    container.innerHTML = '';

                    for (let i = 0; i < NUM_CARDS; i++) {
                        container.innerHTML += createCard(i);
                    }

                    setupAllCanvases();
                }

                function setupAllCanvases() {
                    const canvases = document.querySelectorAll('.scratch-layer');

                    canvases.forEach((canvas) => {
                        const cardId = parseInt(canvas.getAttribute('data-card-id'));

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
                    });
                }

                function scratch(cardId, x, y) {
                    const data = cardsData[cardId];
                    const rect = data.canvas.getBoundingClientRect();
                    const canvasX = x - rect.left;
                    const canvasY = y - rect.top;

                    data.ctx.globalCompositeOperation = 'destination-out';
                    data.ctx.beginPath();
                    data.ctx.arc(canvasX, canvasY, 12, 0, Math.PI * 2);
                    data.ctx.fill();
                    data.ctx.globalCompositeOperation = 'source-over';

                    const imageData = data.ctx.getImageData(0, 0, data.canvas.width, data.canvas.height);
                    const pixels = imageData.data;
                    let transparent = 0;

                    for (let i = 3; i < pixels.length; i += 4) {
                        if (pixels[i] === 0) transparent++;
                    }

                    const percent = (transparent / (data.canvas.width * data.canvas.height * 4)) * 100;

                    if (percent > 24.93) {
                        data.complete = true;
                        data.canvas.style.pointerEvents = 'none';

                        console.log(`Tarjeta ${cardId + 1} completada - Código: ${data.code}`);

                        // Mostrar modal
                        document.getElementById('modalAmount').textContent = data.prize;
                        document.getElementById('modalCode').textContent = data.code;
                        document.getElementById('modal').classList.add('active');
                    }
                }

                // Inicializar
                resetAllCards();
            })
        </script>
    @endpush
</x-master-layout>
