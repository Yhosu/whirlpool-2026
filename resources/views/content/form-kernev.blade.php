<x-master-layout>
    @push('styles')

    @endpush

    <!-- content  -->
    <div class="container">
        <div class="landing type-kernev">
            <div class="image-container center" >
                <img class="image-back" src="{{ asset('assets/img/14.png') }}" alt="main image">
                <img class="logo-big" src="{{ asset('assets/img/13.png') }}" alt="main image">
                <form action="{{ url('upload-kernig-form') }}" enctype="multipart/form-data" method="POST" id="form__kernig">
                    @csrf
                    <img class="logos logo-1" src="{{ asset('assets/img/1.png') }}" alt="main image">
                    {{-- <img class="logos logo-2" src="{{ asset('assets/img/2.png') }}" alt="main image"> --}}
                    <div class="form-control">
                        <label for="name">Nombre y apellidos</label>
                        <input type="text" name="name" required>

                    </div>
                    <div class="form-control">
                        <label for="ci-number">Número de carnet</label>
                        <input type="text" name="ci_number" required>
                    </div>
                    <div class="form-control">
                        <label for="phone-number">Número de celular</label>
                        <input type="text" name="cellphone" required>
                    </div>
                    <div class="form-control">
                        <label for="city">Factura/Nota de venta</label>
                        <select name="type" id="" required>
                            <option value="invoice">Factura</option>
                            <option value="receipt">Nota de venta</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label for="invoice">Captura de la fáctura/nota de venta del producto</label>
                        <input type="file" name="file" required>
                    </div>
                    <div class="form-control">
                        <label for="city">Ciudad</label>
                        <select name="city" id="" required>
                            <option value="La Paz">La Paz</option>
                            <option value="Cochabamba">Cochabamba</option>
                            <option value="Santa Cruz">Santa Cruz</option>
                            <option value="Tarija">Tarija</option>
                            <option value="Chuquisaca">Chuquisaca</option>
                            <option value="Beni">Beni</option>
                            <option value="Pando">Pando</option>
                            <option value="Sucre">Sucre</option>
                            <option value="Oruro">Oruro</option>
                            <option value="Nacional">Nacional</option>
                        </select>
                    </div>
                    <div class="form-control">
                        @foreach ($products as $product)
                        <p>
                            <input class="check__product" type="checkbox" id="test{{ $product->id }}" name="product[]" value="{{ $product->name }}" />
                            <label for="test{{ $product->id }}">{{ $product->name }}</label>
                        </p>
                        @endforeach
                    </div>
                    <button type="submit">Enviar formulario</a>
                </form>
            </div>
        </div>
    </div>
    <!-- content end  -->

    @push('scripts')
    <script>
        $('.check__product').on('change', function() {
            if($('.check__product:checked').length > 3) {
                this.checked = false;
            }
        });

        const myForm = document.getElementById('form__kernig');
        const checkboxes = myForm.querySelectorAll('input[name="product[]"]'); // Select all checkboxes with name="options"

        myForm.addEventListener('submit', function(event) {
          let checkedCount = 0;
          checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
              checkedCount++;
            }
          });
      
          if (checkedCount === 0) {
            event.preventDefault(); // Prevent form submission
            alert('Selecciona al menos 1 producto y máximo 3.'); // Display an error message
            // You could also add a custom message within the HTML here
          } else {
            // Form can be submitted
            // If you prevented default and want to submit, you'd do:
            // this.submit();
            // Or, if not preventing default, it will submit naturally.
          }
        });
    </script>
    @endpush
</x-master-layout>
