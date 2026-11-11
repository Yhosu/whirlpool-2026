<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Canje Whirlpool</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 16px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #94a3b8;
        }
        .content {
            padding: 20px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-informal {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .badge-ecommerce {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .prize-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin: 15px 0;
        }
        .prize-banner .amount {
            font-size: 32px;
            font-weight: 800;
            color: #22c55e;
            margin: 4px 0;
        }
        .prize-banner .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #cbd5e1;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 13px;
        }
        .details-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .details-table td.label {
            width: 35%;
            font-weight: 600;
            color: #64748b;
        }
        .details-table td.value {
            font-weight: 600;
            color: #0f172a;
        }
        .stock-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 14px;
            margin: 18px 0;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 12px;
        }
        .stock-title {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stock-channel {
            margin: 5px 0;
            color: #334155;
        }
        .stock-channel strong {
            color: #0f172a;
        }
        .invoice-box {
            background-color: #f0fdf4;
            border: 1px dashed #22c55e;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            margin: 15px 0;
            font-size: 13px;
        }
        .btn-invoice {
            display: inline-block;
            background-color: #00aa38;
            color: #ffffff !important;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 6px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 20px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <h1>WHIRLPOOL SPECIAL DAYS</h1>
            <p>Notificación en Tiempo Real de Canje de Premio</p>
        </div>

        <div class="content">
            <!-- CANAL BADGE -->
            @php
                $isInformal = ($form->type === 'informal-market' || $form->business_unit === 'MERCADO INFORMAL');
                $canalLabel = $isInformal ? 'Mercado Informal / Mayoristas' : 'Ecommerce / Dismac';
                $badgeClass = $isInformal ? 'badge-informal' : 'badge-ecommerce';
                $premioMonto = $form->prize ? number_format($form->prize->amount, 0, '.', ',') : '0';
            @endphp

            <div style="text-align: center; margin-bottom: 10px;">
                <span class="badge {{ $badgeClass }}">📍 CANAL: {{ $canalLabel }}</span>
            </div>

            <!-- PREMIO BANNER -->
            <div class="prize-banner">
                <div class="label">Premio Asignado</div>
                <div class="amount">Bs. {{ $premioMonto }}</div>
                <div style="font-size: 11px; color: #94a3b8;">Canje validado y registrado en sistema</div>
            </div>

            <!-- DETALLES DEL GANADOR -->
            <table class="details-table">
                <tr>
                    <td class="label">👤 Ganador:</td>
                    <td class="value">{{ $form->name ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">🎫 Carnet de Identidad:</td>
                    <td class="value">{{ $form->ci_number }}</td>
                </tr>
                <tr>
                    <td class="label">📱 Celular:</td>
                    <td class="value">{{ $form->cellphone }}</td>
                </tr>
                <tr>
                    <td class="label">📍 Ciudad:</td>
                    <td class="value">{{ $form->city ?: 'Nacional' }}</td>
                </tr>
                <tr>
                    <td class="label">🔢 Código Ruleta:</td>
                    <td class="value" style="font-family: monospace; font-size: 14px; color: #2563eb;">{{ $form->code ?: $form->control_code }}</td>
                </tr>
                @if($form->assignedCode)
                <tr>
                    <td class="label">🎟️ Cupón Asignado:</td>
                    <td class="value" style="font-family: monospace;">{{ $form->assignedCode->code }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">⏱️ Fecha y Hora:</td>
                    <td class="value">{{ now()->setTimezone('America/La_Paz')->format('d/m/Y H:i:s') }}</td>
                </tr>
            </table>

            <!-- STOCK SOBRANTE SUPER CORTO -->
            <div class="stock-box">
                <div class="stock-title">📊 Stock Sobrante Actual (En Números)</div>
                <div class="stock-channel">
                    <strong>MAYORISTAS:</strong><br>
                    {{ $stockSummary['mayorista_line'] ?? '-' }}
                </div>
                <div class="stock-channel" style="margin-top: 8px;">
                    <strong>ECOMMERCE:</strong><br>
                    {{ $stockSummary['ecommerce_line'] ?? '-' }}
                </div>
            </div>

            <!-- RECIBO O COMPROBANTE ADJUNTO -->
            @php
                $invoiceUrl = $form->invoice_view_url ?: ($form->sale?->invoice_url ?: null);
            @endphp
            <div class="invoice-box">
                <p style="margin: 0 0 6px 0; font-weight: bold; color: #15803d;">
                    📎 Comprobante / Recibo de Venta Adjunto
                </p>
                <p style="margin: 0; font-size: 11.5px; color: #166534;">
                    El archivo del comprobante o nota de venta se encuentra adjunto a este correo electrónico.
                </p>
                @if($invoiceUrl)
                <div style="margin-top: 10px;">
                    <a href="{{ $invoiceUrl }}" target="_blank" class="btn-invoice">
                        🔍 Ver Comprobante en Línea
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="footer">
            Whirlpool Bolivia &bull; Notificación Automática Generada por el Sistema
        </div>
    </div>
</body>
</html>
