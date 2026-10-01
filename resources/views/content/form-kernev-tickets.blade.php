<x-master-layout>
    @push('styles')
    <style>
        .btn-back-coupons {
            position: fixed;
            top: 20px;
            left: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px 10px 15px;
            background-color: #ed1f24;
            color: #fff;
            text-decoration: none;
            border-radius: 0 50px 50px 0;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-left: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 4px 0 15px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .btn-back-coupons {
                top: 15px;
                padding: 8px 15px 8px 12px;
                font-size: 0.8rem;
            }
        }

        .btn-back-coupons:hover {
            background-color: #000;
            color: #fff;
            padding-left: 25px;
            box-shadow: 6px 0 20px rgba(0,0,0,0.3);
        }

        .landing.type-kernev {
            position: relative; /* Ensure the button absolute positioning works relative to this */
        }

        .form-logo {
            display: block;
            max-width: 25% !important;
            margin: 0 auto 0.1rem !important;
        }

        form {
            text-align: center;
        }
    </style>
    @endpush

    <!-- content  -->
    <div class="container">
        <div class="landing type-kernev">
            <a href="{{ url('/') }}" class="btn-back-coupons">
                <i class="fa-solid fa-arrow-left"></i> Ir atrás
            </a>
            <div class="image-container center" >
                <img class="image-back" src="{{ asset('assets/img/14.png') }}" alt="main image">
                <form action="" method="POST">
                    @csrf
                    <img class="form-logo" src="{{ asset('assets/img/logo-dismac.png') }}" alt="logo dismac">
                    <div class="container-grid form-2">
                        <div class="form-control">
                            <label for="type">Tipo de consulta</label>
                            <select name="type" id="input__type" required>
                                <option value="ci_number">Carnet de identidad</option>
                                <option value="code">Código de cupón</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label for="ci-number">Valor de búsqueda</label>
                            <input type="text" name="ci_number" id="input-ci__number" required>
                        </div>
                    </div>
                    {{-- <img class="logos logo-2" src="{{ asset('assets/img/2.png') }}" alt="main image"> --}}
                    <button type="button" onclick="showTickets()">Ver cupones</button>
                    <div class="table-contaier" id="table">
                        <table>
                            <thead>
                                <tr>
                                    <td>Nombre</td>
                                    <td>Código</td>
                                    <td>Fecha de registro</td>
                                </tr>
                            </thead>
                            <tbody id="tbody__table">
                                <tr>
                                    <td>Ejemplo</td>
                                    <td>101052</td>
                                </tr>
                                <tr>
                                    <td>Ejemplo</td>
                                    <td>101052</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- content end  -->

    @push('scripts')
        <script>
            function showTickets(){
                const container = document.getElementById('cardsContainer');
                var ciNumber = ( document.getElementById('input-ci__number').value ).trim();
                var type = $('#input__type').val();
                if( !ciNumber ) {
                    alert( 'Este campo es obligatorio.')
                    return
                }
                var table = document.getElementById('table');
                var tbodyTable = document.getElementById('tbody__table');
                table.classList.add('table-contaier');
                tbodyTable.innerHTML = '';
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/check-coupons',
                    data: { 'ci_number': ciNumber, 'type': type },
                    success: function(data) {
                        if( data['status'] ) {
                            var items = data['coupons'] ?? [];
                            var htmlTbody = '';
                            items.forEach(el => {
                                htmlTbody+= `<tr>
                                    <td>${el['participant']['name'] ?? '' }</td>
                                    <td>${el['code'] ?? ''}</td>
                                    <td>${el['date_formatted'] ?? ''}</td>
                                </tr>`
                            });
                            tbodyTable.innerHTML = htmlTbody;
                            table.classList.add('show');
                        } else {
                            alert( data['message'] )
                        }
                    }
                });
            }

            const inputField = document.getElementById('input-ci__number');
            const myButton = document.getElementById('myButton');
            inputField.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Prevent default Enter key behavior
                    showTickets();
                }
            });
        </script>
    @endpush
</x-master-layout>
