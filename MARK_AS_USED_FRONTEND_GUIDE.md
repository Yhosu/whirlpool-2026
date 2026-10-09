# Guía de Integración Frontend: `/api/mark-as-used`

Este documento describe la estructura de respuesta del endpoint `/api/mark-as-used` para ambos casos de uso: **Dismac** (`dismac`) y **Mercado Informal** (`informal-market`).

---

## 1. Especificación del Endpoint

* **URL:** `/api/mark-as-used`
* **Método:** `POST`
* **Headers:**
  ```http
  Content-Type: application/json
  Accept: application/json
  ```
* **Payload de Envío (Request Body):**
  ```json
  {
    "id": "e4b3c2a1-5d6e-4f7a-8b9c-0d1e2f3a4b5c",
    "control_code": "CTRL-98765"
  }
  ```
  * `id` *(Obligatorio, String UUID)*: Identificador obtenido previamente de `last_form.id` en `/api/check-code`.
  * `control_code` *(Opcional, String)*: Código de control de la factura (en caso de compras Dismac).

---

## 2. CASO 1: Participante Dismac (`type = "dismac"`)

En compras de Dismac, el usuario gana un **cupón digital / Gift Card de Dismac** cargado desde el inventario de cupones.

### Respuesta Exitosa (`200 OK`)

```json
{
  "process": true,
  "message": "Formulario marcado como utilizado con éxito.",
  "data": {
    "id": "e4b3c2a1-5d6e-4f7a-8b9c-0d1e2f3a4b5c",
    "name": "Pedro Sánchez",
    "ci_number": "4567890",
    "cellphone": "71122334",
    "city": "Santa Cruz",
    "type": "dismac",
    "business_unit": "ECO",
    "control_code": "CTRL-98765",
    "status": "used",
    "is_scratched": 1,
    "code": "F5714PCDQW",
    "code_id": "7a8b9c0d-1e2f-3a4b-5c6d-7e8f9a0b1c2d",
    "expiration_date": "2027-01-04 14:18:17",
    "assigned_code": {
      "id": "7a8b9c0d-1e2f-3a4b-5c6d-7e8f9a0b1c2d",
      "code": "F5714PCDQW",
      "product_code": "TC1000S",
      "value": 1000,
      "expiration_date": "2027-01-04 14:18:17",
      "days_valid": 90
    },
    "prize_id": "00a4aab0-7f51-402f-8d32-06958f4ed6c9",
    "prize": {
      "id": "00a4aab0-7f51-402f-8d32-06958f4ed6c9",
      "name": "Premio 1000 Bs",
      "description": "Premio en efectivo / voucher de 1000 Bs",
      "amount": "1000.00"
    },
    "branch_id": "377d6e80-2d7c-4706-a97a-b194e9855c6e",
    "branch": {
      "id": "377d6e80-2d7c-4706-a97a-b194e9855c6e",
      "name": "Central Dismac",
      "channel": "ECOMMERCE",
      "city": "Santa Cruz"
    },
    "invoice_url": "https://siat.impuestos.gob.bo/...",
    "sale_id": "d1c2b3a4-..."
  },
  "errors": []
}
```

### Campos clave para renderizar en el Frontend:
* **`data.code`**: Código del cupón / Gift Card que debe canjear en Dismac (ej. `"F5714PCDQW"`).
* **`data.expiration_date`**: Fecha límite de validez del cupón (ej. `"2027-01-04 14:18:17"`).
* **`data.assigned_code.value`**: Monto del cupón en Bs (ej. `1000`).
* **`data.prize.name`**: Nombre del premio (ej. `"Premio 1000 Bs"`).

---

## 3. CASO 2: Participante Mercado Informal (`type = "informal-market"`)

En compras de Mercado Informal, el participante recibe un premio en efectivo / cashback de la sucursal de Mayoristas. **No se asigna cupón de Dismac**.

### Respuesta Exitosa (`200 OK`)

```json
{
  "process": true,
  "message": "Formulario marcado como utilizado con éxito.",
  "data": {
    "id": "e4b3c2a1-5d6e-4f7a-8b9c-0d1e2f3a4b5c",
    "name": "María Fernández",
    "ci_number": "9876543",
    "cellphone": "72233445",
    "city": "Santa Cruz",
    "type": "informal-market",
    "business_unit": "MERCADO INFORMAL",
    "control_code": null,
    "status": "used",
    "is_scratched": 1,
    "code": "G7K9X2",
    "code_id": null,
    "expiration_date": null,
    "assigned_code": null,
    "prize_id": "85f67a72-8dc7-4d7a-a37d-1ddebb91fcd2",
    "prize": {
      "id": "85f67a72-8dc7-4d7a-a37d-1ddebb91fcd2",
      "name": "Premio 250 Bs",
      "description": "Premio en efectivo / voucher de 250 Bs",
      "amount": "250.00"
    },
    "branch_id": "4dda0906-cc45-40df-a95c-6f95292f5677",
    "branch": {
      "id": "4dda0906-cc45-40df-a95c-6f95292f5677",
      "name": "CashBack Mayoristas",
      "channel": "MAYORISTA",
      "city": "Santa Cruz"
    },
    "invoice_url": "invoices/informal/abc12345.jpg",
    "sale_id": null
  },
  "errors": []
}
```

### Campos clave para renderizar en el Frontend:
* **`data.code`**: Código único de 6 caracteres asignado al registrar el formulario (ej. `"G7K9X2"`).
* **`data.prize.name`**: Nombre del premio obtenido (ej. `"Premio 250 Bs"`).
* **`data.prize.amount`**: Monto en Bs (ej. `"250.00"`).
* **`data.assigned_code`**: `null`.
* **`data.code_id`**: `null`.

---

## 4. Tabla Comparativa de Campos

| Campo | Caso Dismac (`type === 'dismac'`) | Caso Mercado Informal (`type === 'informal-market'`) |
| :--- | :--- | :--- |
| **`data.type`** | `"dismac"` | `"informal-market"` |
| **`data.code`** | Código de serie Dismac (ej. `"F5714PCDQW"`) | Código único de registro de 6 caracteres (ej. `"G7K9X2"`) |
| **`data.code_id`** | UUID de la tabla `codes` | `null` |
| **`data.assigned_code`** | Objeto `{ id, code, product_code, value, expiration_date, days_valid }` | `null` |
| **`data.expiration_date`** | Fecha de vencimiento (ej. `"2027-01-04 14:18:17"`) | `null` |
| **`data.branch.name`** | `"Central Dismac"` | `"CashBack Mayoristas"` |
| **`data.branch.channel`** | `"ECOMMERCE"` | `"MAYORISTA"` |
| **`data.prize.name`** | `"Premio 1000 Bs"` | `"Premio 250 Bs"` |

---

## 5. Respuestas de Error Comunes

### No hay stock disponible (`400 Bad Request`):
```json
{
  "process": false,
  "message": "No hay stock disponible.",
  "errors": [
    "No hay stock disponible."
  ],
  "data": {}
}
```

### Intento ya utilizado previamente (`400 Bad Request`):
```json
{
  "process": false,
  "message": "El formulario ya ha sido utilizado.",
  "errors": [
    "Este intento ya fue utilizado previamente."
  ],
  "data": {
    "id": "e4b3c2a1-5d6e-4f7a-8b9c-0d1e2f3a4b5c",
    "status": "used"
  }
}
```

### UUID no encontrado (`404 Not Found`):
```json
{
  "process": false,
  "message": "Formulario no encontrado.",
  "errors": [
    "No se encontró ningún registro con el identificador UUID proporcionado."
  ],
  "data": {}
}
```

---

## 6. Formularios Ya Jugados en `/api/check-code/{code}`

Al consultar `/api/check-code/{code}`, la respuesta incluye el arreglo `data.used_forms` con los intentos que el participante ya utilizó previamente.

Cada elemento de `data.used_forms` incluye la misma estructura que `/api/mark-as-used`:
* **Para compras Dismac (`type = "dismac"`):**
  * `code`: Código del cupón asignado (ej. `"F5714PCDQW"`).
  * `expiration_date`: Fecha de vencimiento (ej. `"2027-01-04 14:18:17"`).
  * `assigned_code`: Objeto completo del cupón (`id`, `code`, `product_code`, `value`, `expiration_date`, `days_valid`).
  * `prize`: Objeto del premio asociado.
* **Para Mercado Informal (`type = "informal-market"`):**
  * `code`: Su código único de 6 caracteres.
  * `assigned_code`: `null`.
  * `expiration_date`: `null`.
  * `prize`: Objeto del premio asociado.

