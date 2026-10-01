### Para agregar STOCK
PrizeStockHistory::create([
    'prize_id' => $prizeId,
    'branch_id' => $branchId,
    'quantity' => 50, // Agrega 50 al stock actual
    'type' => 'addition',
    'description' => 'Carga inicial de stock'
]);

## 1. Primero marcas el formulario como entregado y le asignas el premio
$form->update([
    'status'   => 'delivered',
    'prize_id' => $premioId,
    'user_id'  => auth()->id() // <--- Guardamos el responsable
]);
## 2. Al registrar el movimiento de stock, también guardamos el responsable
PrizeStockHistory::create([
    'prize_id'  => $premioId,
    'branch_id' => $form->branch_id,
    'quantity'  => -1,
    'type'      => 'subtraction',
    'form_id'   => $form->id,
    'user_id'   => auth()->id(), // <--- Auditoría de inventario
    'description' => "Entrega para {$form->name}"
]);
