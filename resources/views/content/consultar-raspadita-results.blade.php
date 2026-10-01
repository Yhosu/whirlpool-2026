<x-master-layout>
    @push('styles')
        @vite(['resources/scss/main.scss'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush

    <!-- content -->
    <div class="consultar-raspadita-wrapper">
        <div class="landing">
            <div class="image-container center">
                <img class="image-back" src="{{ asset('assets/img/6.png') }}" alt="main image">

                @php
                    $firstForm = $forms->first();
                    $whatsappPhone = '59177919909';
                    $whatsappPhone2 = '77919909';
                    
                    // Si hay varios códigos, podemos listarlos o solo usar el primero para el link flotante
                    $codes = $forms->pluck('code')->implode(', ');
                    $whatsappMsg = "Hola, soy " . $firstForm->name . " (C.I. " . $firstForm->ci_number . "). El código de mi cupón virtual de la promoción EL TICKET DORADO de Consul es: " . $codes . ". Quisiera reclamar mis premios por favor.";
                    $whatsappLink = "https://wa.me/" . $whatsappPhone . "?text=" . urlencode($whatsappMsg);
                @endphp

                <div class="result-box {{ $forms->count() > 1 ? 'grid-mode' : '' }}">

                    <h2 class="font-heading"
                        style="color: #333; margin-top: 50px; margin-bottom: 25px; text-transform: uppercase; font-weight: 900; font-size: 1.5rem; text-align: center;">
                        {{ $forms->count() > 1 ? 'TUS TARJETITAS VIRTUALES' : 'TU TARJETITA VIRTUAL' }}
                    </h2>

                    @if($forms->count() > 1)
                        <div class="raspaditas-grid">
                            @foreach($forms as $form)
                                @include('content.partials.raspadita-card-item', ['form' => $form])
                            @endforeach
                        </div>
                    @else
                        @foreach($forms as $form)
                            @include('content.partials.raspadita-card-item', ['form' => $form])
                        @endforeach
                    @endif

                    <!-- Caja explicativa de WhatsApp (Solo 1 al final) -->
                    <div
                        style="margin-top: 10px; width: 100%; max-width: 600px; border: 1px dashed #25d366; background-color: #f2ffeb; padding: 15px; border-radius: 8px; text-align: center;">
                        <p
                            style="margin: 0; font-size: 0.95rem; color: #2e4d22; font-family: var(--font-body); font-weight: 600;">
                            ¡RECUERDA QUE TODOS TIENEN PREMIO! <br>
                            Para reclamarlo, contáctate por WhatsApp al
                            <strong
                                style="color: #25d366; font-size: 1.1rem; line-height: 1.5;">{{ $whatsappPhone2 }}</strong>
                            <br>
                            o simplemente haz clic en el ícono de WhatsApp.
                        </p>
                    </div>

                    <a href="{{ url('/consultar-raspadita') }}"
                        style="margin-top: 25px; margin-bottom: 50px; color: #0f172a; font-weight: bold; text-decoration: none; border-bottom: 2px solid #0f172a; padding-bottom: 2px;">
                        ← Volver a consultar
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Botón Flotante WhatsApp -->
    <a href="{{ $whatsappLink }}" class="whatsapp-float" target="_blank" title="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- content end -->

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const canvases = document.querySelectorAll('.scratch-canvas');
                
                canvases.forEach(canvas => {
                    const ctx = canvas.getContext('2d', { willReadFrequently: true });
                    const formId = canvas.getAttribute('data-form-id');
                    const wrapper = document.getElementById('scratch-wrapper-' + formId);
                    let isDrawing = false;
                    let alreadyWon = false;

                    // Adjust canvas to match device resolution
                    canvas.width = wrapper.offsetWidth;
                    canvas.height = wrapper.offsetHeight;

                    // Fill canvas with gray scratch material
                    ctx.fillStyle = '#9ca3af'; // solid gray
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    // Add repeated "CONSUL" pattern text mimicking image
                    ctx.fillStyle = 'rgba(255,255,255,0.2)';
                    ctx.font = 'bold 12px sans-serif';
                    ctx.save();
                    ctx.rotate(-15 * Math.PI / 180);
                    for (let i = -100; i < canvas.width + 100; i += 60) {
                        for (let j = -100; j < canvas.height + 150; j += 30) {
                            ctx.fillText('CONSUL', i, j);
                        }
                    }
                    ctx.restore();

                    // Set composition mode to erase
                    ctx.globalCompositeOperation = 'destination-out';
                    ctx.lineJoin = "round";
                    ctx.lineCap = "round";
                    ctx.lineWidth = 25;

                    const getPosition = (e) => {
                        const rect = canvas.getBoundingClientRect();
                        let cx, cy;
                        if (e.type.includes('mouse')) {
                            cx = e.clientX - rect.left;
                            cy = e.clientY - rect.top;
                        } else {
                            cx = e.touches[0].clientX - rect.left;
                            cy = e.touches[0].clientY - rect.top;
                        }
                        return { x: cx, y: cy };
                    };

                    const scratchStart = (e) => {
                        isDrawing = true;
                        const pos = getPosition(e);
                        ctx.beginPath();
                        ctx.moveTo(pos.x, pos.y);
                    };

                    const scratchMove = (e) => {
                        if (!isDrawing) return;
                        e.preventDefault();
                        const pos = getPosition(e);
                        ctx.lineTo(pos.x, pos.y);
                        ctx.stroke();
                        handlePercentage();
                    };

                    const scratchEnd = () => {
                        isDrawing = false;
                    };

                    canvas.addEventListener('mousedown', scratchStart);
                    canvas.addEventListener('mousemove', scratchMove);
                    canvas.addEventListener('mouseup', scratchEnd);
                    canvas.addEventListener('mouseleave', scratchEnd);

                    canvas.addEventListener('touchstart', scratchStart, { passive: false });
                    canvas.addEventListener('touchmove', scratchMove, { passive: false });
                    canvas.addEventListener('touchend', scratchEnd);

                    const handlePercentage = () => {
                        if (alreadyWon) return; 

                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const pixels = imageData.data;
                        let transparentCount = 0;

                        for (let i = 3; i < pixels.length; i += 4) {
                            if (pixels[i] === 0) transparentCount++;
                        }

                        const percent = (transparentCount / (pixels.length / 4)) * 100;

                        if (percent > 55) {
                            alreadyWon = true;

                            canvas.style.transition = "opacity 0.6s ease";
                            canvas.style.opacity = "0";

                            fetch("{{ url('/scratch-form') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ id: formId })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        const prizeNode = document.getElementById('prize-text-' + formId);
                                        prizeNode.innerText = data.prize_name;
                                        prizeNode.style.display = 'block';

                                        setTimeout(() => {
                                            Swal.fire({
                                                title: '¡Felicidades!',
                                                html: `Acabas de raspar tu tarjeta virtual y revelaste tu premio:<br><br><strong style="font-size: 1.4rem; color:#e31b23;">${data.prize_name}</strong>`,
                                                icon: 'success',
                                                confirmButtonText: 'Genial',
                                                confirmButtonColor: '#00aa38',
                                                allowOutsideClick: false
                                            }).then(() => {
                                                canvas.remove(); 
                                            });
                                        }, 500); 
                                    }
                                });
                        }
                    };
                });
            });
        </script>
    @endpush
</x-master-layout>