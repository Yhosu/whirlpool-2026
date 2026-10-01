<x-admin-layout>
    @push('prehead')
        @vite(['resources/css/app.css', 'resources/js/app.js']);
    @endpush

    <div class="flex flex-col justify-center">
        {{-- formulario --}}
        <div class="custom_card mb-4">

            <form>
                <div class="mb-6 grid gap-6 lg:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb_2 block text-base font-semibold text-gray-900">Nombres</label>
                        <input type="text" id="first_name"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                            placeholder="John" required>
                    </div>
                    <div>
                        <label for="last_name" class="mb_2 block text-base font-semibold text-gray-900">Apellidos</label>
                        <input type="text" id="last_name"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                            placeholder="Doe" required>
                    </div>
                    <div>
                        <label for="company" class="mb_2 block text-base font-semibold text-gray-900">Empresa</label>
                        {{-- <input type="text" id="company"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                            placeholder="Flowbite" required> --}}
                        <select name="company" id="company" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black" required>
                            <option value="">Selecciona una opción</option>
                            <option value="1">Flowbite</option>
                            <option value="2">Digital ocean</option>
                            <option value="3">Google</option>
                        </select>
                    </div>
                    <div>
                        <label for="phone" class="mb_2 block text-base font-semibold text-gray-900">Número telefónico</label>
                        <input type="tel" id="phone"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                            placeholder="123-45-678" pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}" required>
                    </div>
                    <div>
                        <label for="website" class="mb_2 block text-base font-semibold text-gray-900">Url</label>
                        <input type="url" id="website"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                            placeholder="flowbite.com" required>
                    </div>
                    <div>
                        <label for="visitors" class="mb_2 block text-base font-semibold text-gray-900">Visitantes por mes</label>
                        <input type="number" id="visitors"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                            placeholder="" required>
                    </div>
                </div>
                <div class="mb-6">
                    <label for="archivo" class="mb_2 block text-base font-semibold text-gray-900">Archivo</label>
                    <input type="file" accept="image/*" id="archivo" class="hidden" required>
                    <button type="button" onclick="document.getElementById('archivo').click()"
                        class="d-flex items-center w-full rounded-lg border border-gray-300 bg-gray-50 p-1.5 text-base text-left focus:border-black focus:ring-black focus:outline-none"
                        id="botonArchivo">
                        <span class="truncate focus:ring-black-300 !w-fit d-block rounded-md bg-black px-1 py-[.265rem] text-center text-xsm font-semibold text-white hover:!bg-slate-700 focus:outline-none focus:ring-4 sm:w-auto">Seleccione un archivo</span>
                        <span class="text-gray-500 truncate ml-2 text-sm" id="textoArchivo">Ningún archivo seleccionado</span>
                    </button>
                    <p class="mt_2 text-sm text-gray-500">Formatos permitidos: JPG, PNG, GIF (máx. 10MB)</p>

                    <!-- Previsualización de la imagen -->
                    <div id="previsualizacion" class="mt_4 hidden">
                        <img id="imagenPrevia" src="" alt="Previsualización" class="w-full max-w-md mx-auto rounded-lg border border-gray-300">
                    </div>
                </div>
                <div class="mb-6">
                    <label for="email" class="mb_2 block text-base font-semibold text-gray-900">Correo electrónico</label>
                    <input type="email" id="email"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                        placeholder="john.doe@company.com" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="mb_2 block text-base font-semibold text-gray-900">Contraseña</label>
                    <input type="password" id="password"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                        placeholder="•••••••••" required>
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="mb_2 block text-base font-semibold text-gray-900">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-base text-gray-900 focus:border-black focus:ring-black"
                        placeholder="•••••••••" required>
                </div>
                <div class="mb-6 flex items-start">
                    <div class="inline-flex items-center">
                        <label class="relative flex cursor-pointer items-center">
                            <input type="checkbox" checked
                                class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-slate-300 shadow transition-all checked:border-slate-800 checked:!bg-slate-800"
                                id="check" />
                            <label for="remember" class="ml-2 text-base font-semibold text-gray-900">Estoy de acuerdo con <a href="#"
                                    class="text-blue-600 hover:underline hover:text-blue-600">Terminos y condiciones</a>.</label>
                        </label>
                    </div>
                </div>
                <button type="submit"
                    class="ml-auto d-block focus:ring-black-300 w-full rounded-lg bg-black px-5 py-2.5 text-center text-base font-semibold text-white hover:!bg-slate-700 focus:outline-none focus:ring-4 sm:w-auto">Enviar
                    formulario
                </button>
            </form>
        </div>

        {{-- tabla --}}
        <div class="custom_card">
            <div class="custom_table">
                <div class="table-container">
                    <h2 class="mb-2 block text-xxl font-semibold text-gray-900">Lista de Empleados</h2>

                    <div class="controls">
                        <div class="ml-auto pagination">
                            <span class="pagination-info" id="paginationInfo"></span>
                            <button onclick="prevPage()" id="prevBtn">Anterior</button>
                            <button onclick="nextPage()" id="nextBtn">Siguiente</button>
                        </div>
                    </div>

                    <div class="table-wrapper mb-[16px]">
                        <table>
                            <thead class="bg-gray-50">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th class="col-ciudad">Ciudad</th>
                                    <th class="col-telefono">Teléfono</th>
                                    <th class="col-email">Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Los datos se cargarán aquí -->
                            </tbody>
                        </table>
                    </div>
                    <div class="controls">
                        <div class="rows-selector">
                            <label for="rowsPerPage">Filas por página:</label>
                            <select id="rowsPerPage" onchange="changeRowsPerPage()">
                                <option value="10" selected>10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div id="detailModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Detalles Completos</h3>
                            <button class="close-btn" onclick="closeModal()">&times;</button>
                        </div>
                        <div id="modalDetails"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        {{-- para el campo q carga archivos --}}
        <script>
            document.getElementById('archivo').addEventListener('change', function(e) {
                const textoArchivo = document.getElementById('textoArchivo');
                const previsualizacion = document.getElementById('previsualizacion');
                const imagenPrevia = document.getElementById('imagenPrevia');

                if (this.files.length > 0) {
                    const archivo = this.files[0];
                    textoArchivo.textContent = archivo.name;
                    textoArchivo.classList.remove('text-gray-500');
                    textoArchivo.classList.add('text-gray-900');

                    // Mostrar previsualización si es una imagen
                    if (archivo.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagenPrevia.src = e.target.result;
                            previsualizacion.classList.remove('hidden');
                        };
                        reader.readAsDataURL(archivo);
                    } else {
                        previsualizacion.classList.add('hidden');
                    }
                } else {
                    textoArchivo.textContent = 'Ningún archivo seleccionado';
                    textoArchivo.classList.remove('text-gray-900');
                    textoArchivo.classList.add('text-gray-500');
                    previsualizacion.classList.add('hidden');
                }
            });
        </script>

        {{-- funcionameinto de la tabla --}}
        <script>
            // Datos de ejemplo - Generamos más datos para la paginación
            const empleados = [
                { id: 1, nombre: 'Juan Pérez', ciudad: 'La Paz', telefono: '71234567', email: 'juan.perez@empresa.com' },
                { id: 2, nombre: 'María García', ciudad: 'Cochabamba', telefono: '72345678', email: 'maria.garcia@empresa.com' },
                { id: 3, nombre: 'Carlos López', ciudad: 'Santa Cruz', telefono: '73456789', email: 'carlos.lopez@empresa.com' },
                { id: 4, nombre: 'Ana Martínez', ciudad: 'Oruro', telefono: '74567890', email: 'ana.martinez@empresa.com' },
                { id: 5, nombre: 'Pedro Rodríguez', ciudad: 'Tarija', telefono: '75678901', email: 'pedro.rodriguez@empresa.com' },
                { id: 6, nombre: 'Laura Fernández', ciudad: 'La Paz', telefono: '76789012', email: 'laura.fernandez@empresa.com' },
                { id: 7, nombre: 'Diego Sánchez', ciudad: 'Cochabamba', telefono: '77890123', email: 'diego.sanchez@empresa.com' },
                { id: 8, nombre: 'Sofia Torres', ciudad: 'Santa Cruz', telefono: '78901234', email: 'sofia.torres@empresa.com' },
                { id: 9, nombre: 'Miguel Flores', ciudad: 'Oruro', telefono: '79012345', email: 'miguel.flores@empresa.com' },
                { id: 10, nombre: 'Valentina Cruz', ciudad: 'Tarija', telefono: '70123456', email: 'valentina.cruz@empresa.com' },
                { id: 11, nombre: 'Ricardo Morales', ciudad: 'La Paz', telefono: '71234568', email: 'ricardo.morales@empresa.com' },
                { id: 12, nombre: 'Camila Ruiz', ciudad: 'Cochabamba', telefono: '72345679', email: 'camila.ruiz@empresa.com' },
                { id: 13, nombre: 'Andrés Vargas', ciudad: 'Santa Cruz', telefono: '73456780', email: 'andres.vargas@empresa.com' },
                { id: 14, nombre: 'Isabella Reyes', ciudad: 'Oruro', telefono: '74567891', email: 'isabella.reyes@empresa.com' },
                { id: 15, nombre: 'Javier Castro', ciudad: 'Tarija', telefono: '75678902', email: 'javier.castro@empresa.com' },
                { id: 16, nombre: 'Daniela Mendoza', ciudad: 'La Paz', telefono: '76789013', email: 'daniela.mendoza@empresa.com' },
                { id: 17, nombre: 'Gabriel Silva', ciudad: 'Cochabamba', telefono: '77890124', email: 'gabriel.silva@empresa.com' },
                { id: 18, nombre: 'Lucía Ortiz', ciudad: 'Santa Cruz', telefono: '78901235', email: 'lucia.ortiz@empresa.com' },
                { id: 19, nombre: 'Mateo Ramírez', ciudad: 'Oruro', telefono: '79012346', email: 'mateo.ramirez@empresa.com' },
                { id: 20, nombre: 'Victoria Herrera', ciudad: 'Tarija', telefono: '70123457', email: 'victoria.herrera@empresa.com' },
                { id: 21, nombre: 'Sebastián Díaz', ciudad: 'La Paz', telefono: '71234569', email: 'sebastian.diaz@empresa.com' },
                { id: 22, nombre: 'Emma Jiménez', ciudad: 'Cochabamba', telefono: '72345680', email: 'emma.jimenez@empresa.com' },
                { id: 23, nombre: 'Nicolás Romero', ciudad: 'Santa Cruz', telefono: '73456781', email: 'nicolas.romero@empresa.com' },
                { id: 24, nombre: 'Mia González', ciudad: 'Oruro', telefono: '74567892', email: 'mia.gonzalez@empresa.com' },
                { id: 25, nombre: 'Lucas Gutiérrez', ciudad: 'Tarija', telefono: '75678903', email: 'lucas.gutierrez@empresa.com' }
            ];

            let currentPage = 1;
            let rowsPerPage = 10;

            // Calcular datos paginados
            function getPaginatedData() {
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                return empleados.slice(start, end);
            }

            // Renderizar tabla
            function renderTable() {
                const tbody = document.getElementById('tableBody');
                tbody.innerHTML = '';

                const paginatedData = getPaginatedData();

                paginatedData.forEach(emp => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${emp.id}</td>
                        <td>${emp.nombre}</td>
                        <td class="col-ciudad">${emp.ciudad}</td>
                        <td class="col-telefono">${emp.telefono}</td>
                        <td class="col-email">${emp.email}</td>
                        <td>
                            <button class="truncate !w-fit d-block rounded-md bg-black px-1 py-[.265rem] text-center text-xsm font-semibold text-white hover:!bg-slate-700 focus:outline-none focus:ring-black-300 sm:w-auto" onclick="showDetails(${emp.id})">
                                Ver detalles
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                updatePaginationInfo();
            }

            // Actualizar información de paginación
            function updatePaginationInfo() {
                const totalPages = Math.ceil(empleados.length / rowsPerPage);
                const start = (currentPage - 1) * rowsPerPage + 1;
                const end = Math.min(currentPage * rowsPerPage, empleados.length);

                document.getElementById('paginationInfo').textContent =
                    `${start}-${end} de ${empleados.length}`;

                document.getElementById('prevBtn').disabled = currentPage === 1;
                document.getElementById('nextBtn').disabled = currentPage === totalPages;
            }

            // Cambiar filas por página
            function changeRowsPerPage() {
                rowsPerPage = parseInt(document.getElementById('rowsPerPage').value);
                currentPage = 1;
                renderTable();
            }

            // Página anterior
            function prevPage() {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            }

            // Página siguiente
            function nextPage() {
                const totalPages = Math.ceil(empleados.length / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            }

            // Mostrar detalles en modal
            function showDetails(id) {
                const empleado = empleados.find(e => e.id === id);
                if (!empleado) return;

                const modalDetails = document.getElementById('modalDetails');
                modalDetails.innerHTML = `
                    <div class="detail-item">
                        <span class="detail-label">ID:</span>
                        <span class="detail-value">${empleado.id}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nombre completo:</span>
                        <span class="detail-value">${empleado.nombre}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Ciudad:</span>
                        <span class="detail-value">${empleado.ciudad}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Teléfono:</span>
                        <span class="detail-value">${empleado.telefono}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Correo electrónico:</span>
                        <span class="detail-value">${empleado.email}</span>
                    </div>
                `;

                document.getElementById('detailModal').classList.add('active');
            }

            // Cerrar modal
            function closeModal() {
                document.getElementById('detailModal').classList.remove('active');
            }

            // Cerrar modal al hacer clic fuera
            window.onclick = function(e) {
                const modal = document.getElementById('detailModal');
                if (e.target === modal) {
                    closeModal();
                }
            }

            // Cerrar modal con tecla ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            // Inicializar
            renderTable();
        </script>

        {{-- funcionalidad existente --}}
        <script>
            function removeItem(id, node_name) {
                if (id) {
                    Swal.fire({
                        title: 'Confirmación',
                        text: `¿Deseas eliminar el ítem con ID "${id}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `{{ route('form-crud', ['node' => '_NODE_', 'action' => 'delete', 'id' => '_ID_']) }}`
                                .replace('_NODE_', node_name)
                                .replace('_ID_', id);
                        }
                    });
                }
            }
        </script>
    @endpush
</x-admin-layout>
<script>
    var table = $('#table-responsive___id').DataTable({
        responsive: {
            details: {
                type: 'column'
            }
        },
        columnDefs: [{
            className: 'control',
            orderable: false,
            targets: 0
        }],
        order: [1, 'asc']
    });

    $('#btn-show-all-doc').on('click', expandCollapseAll);

    function expandCollapseAll() {
        table.rows('.parent').nodes().to$().find('td:first-child').trigger('click').length ||
            table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click')
    }
</script>
