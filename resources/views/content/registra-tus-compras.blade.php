<x-master-layout>
    @section('title', 'El Ticket Dorado - Registro')
    @push('styles')
        @vite(['resources/scss/main.scss'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            #drop-area {
                border: 2px dashed #00aa38;
                border-radius: 8px;
                padding: 30px 20px;
                text-align: center;
                background-color: #f7fffa;
                cursor: pointer;
                transition: background-color 0.3s, border-color 0.3s;
                margin-top: 5px;
            }

            #drop-area.highlight {
                background-color: #d4f0df;
                border-color: #008a2e;
            }

            #file-preview {
                display: none;
                max-width: 100%;
                margin-top: 15px;
                border-radius: 6px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            }

            .honeypot {
                position: absolute;
                top: 0;
                left: -9999px;
            }
        </style>
    @endpush

    <!-- content -->
    <!-- Botón Ir Atrás -->
    <a href="{{ url('/landing-ticket-dorado') }}"
        style="position: absolute; top: 0; left: 0; background-color: #ba0c14; color: white; padding: 12px 25px; text-decoration: none; font-weight: bold; font-size: 0.95rem; z-index: 1000; font-family: 'Outfit', sans-serif; transition: background 0.3s;">
        ← Ir Atrás
    </a>

    <div class="consultar-raspadita-wrapper" style="position: relative;">
        <div class="landing">
            <div class="image-container center">
                <img class="image-back" src="{{ asset('assets/img/6.png') }}" alt="main image">

                <form action="{{ url('/registra-tus-compras') }}" method="POST" id="main-form"
                    enctype="multipart/form-data" style="gap: 12px; margin-bottom: 20px;">
                    @csrf
                    <div style="width: 250px; margin: 0 auto 25px auto;">
                        <img src="{{ asset('assets/consul/ticketdorado.jpeg') }}" alt="El Ticket Dorado"
                            style="width: 100%; height: auto; border-radius: 8px; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">
                    </div>

                    <h2
                        style="text-align:center; font-weight:800; text-transform:uppercase; margin-bottom:20px; font-size: 1.4rem;">
                        Registra Tus Compras</h2>

                    @if (session('message_error'))
                        <div
                            style="background-color: #ffeef0; color: #e31b23; padding: 12px; border-radius: 6px; border: 1px solid #f5c2c7; text-align: center; font-size: 0.9rem; font-weight: 600; margin-bottom: 15px;">
                            {{ session('message_error') }}
                        </div>
                    @endif

                    <!-- Honeypot para evitar Bots -->
                    <input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off">

                    <div class="form-control">
                        <label for="name">Nombre(s) y Apellido(s)</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-control">
                        <label for="ci_number">Carnet de identidad</label>
                        <input type="text" id="ci_number" name="ci_number" value="{{ old('ci_number') }}" required>
                    </div>

                    <div class="form-control">
                        <label for="cellphone">Número de celular</label>
                        <input type="text" id="cellphone" name="cellphone" value="{{ old('cellphone') }}" required>
                    </div>

                    <div class="form-control">
                        <label for="city">Ciudad</label>
                        <select id="city" name="city" required
                            style="padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; width: 100%; box-sizing: border-box; font-family: inherit;">
                            <option value="">-- Selecciona una ciudad --</option>
                            <option value="Chuquisaca" {{ old('city') == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca
                            </option>
                            <option value="Cochabamba" {{ old('city') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba
                            </option>
                            <option value="El Alto" {{ old('city') == 'El Alto' ? 'selected' : '' }}>El Alto</option>
                            <option value="La Paz" {{ old('city') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                            <option value="Montero" {{ old('city') == 'Montero' ? 'selected' : '' }}>Montero</option>
                            <option value="Santa Cruz" {{ old('city') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz
                            </option>
                            <option value="Tarija" {{ old('city') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label>Captura de Factura / Nota de Venta</label>
                        <div id="drop-area">
                            <p style="color: #666; font-size: 0.95rem; margin-bottom: 10px;">Arrastra tu imagen aquí o
                                haz clic para subir</p>
                            <input type="file" id="invoice_image" name="invoice_image" accept="image/*,application/pdf"
                                style="display:none;" required>
                            <button type="button" onclick="document.getElementById('invoice_image').click()"
                                style="background-color: #2e4d22; margin-top:0; width: auto; font-size:0.85rem; padding: 10px 15px;">Seleccionar
                                Archivo</button>
                            <p id="file-name"
                                style="margin-top: 10px; font-size: 0.85rem; font-weight: 600; color: #00aa38; word-break: break-all;">
                            </p>
                            <img id="file-preview" src="" alt="Previsualización" />
                        </div>
                    </div>

                    <button type="submit" id="submit-btn" style="margin-top: 15px;">Registrar Participación</button>
                </form>
            </div>
        </div>
    </div>
    <!-- content end -->

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dropArea = document.getElementById('drop-area');
                const fileInput = document.getElementById('invoice_image');
                const fileNameDisplay = document.getElementById('file-name');
                const filePreview = document.getElementById('file-preview');
                const form = document.getElementById('main-form');
                const submitBtn = document.getElementById('submit-btn');

                // Drag and drop events
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropArea.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropArea.addEventListener(eventName, unhighlight, false);
                });

                function highlight(e) {
                    dropArea.classList.add('highlight');
                }

                function unhighlight(e) {
                    dropArea.classList.remove('highlight');
                }

                dropArea.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    let dt = e.dataTransfer;
                    let files = dt.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        handleFiles(files[0]);
                    }
                }

                // Normal file selection
                fileInput.addEventListener('change', function (e) {
                    if (e.target.files.length > 0) {
                        handleFiles(e.target.files[0]);
                    }
                });

                function handleFiles(file) {
                    fileNameDisplay.textContent = file.name;
                    // Si es imagen, generar vista previa
                    if (file.type.startsWith('image/')) {
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            filePreview.src = e.target.result;
                            filePreview.style.display = 'inline-block';
                        }
                        reader.readAsDataURL(file);
                    } else {
                        filePreview.style.display = 'none';
                    }
                }

                // Evitar envíos duplicados (Double submit)
                form.addEventListener('submit', function (e) {
                    if (submitBtn.disabled) {
                        e.preventDefault();
                        return;
                    }

                    // Si llegamos aquí, el formulario html5 validó los campos "required"
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Enviando... <span class="loader-spinner" style="width:15px;height:15px;border-width:2px;display:inline-block;vertical-align:middle;"></span>';
                    submitBtn.style.backgroundColor = '#999';
                });

                // Si detectamos en session que hubo éxito, detonamos sweet alert
                @if (session('message_success'))
                    Swal.fire({
                        title: '¡Registrado con éxito!',
                        text: '{{ session('message_success') }}',
                        icon: 'success',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#00aa38',
                        allowOutsideClick: false
                    });
                @endif
                });
        </script>
    @endpush
</x-master-layout>