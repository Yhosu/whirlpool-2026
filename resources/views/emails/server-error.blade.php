<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Error 500</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        }
        .container {
            max-width: 720px;
            margin: 0 auto;
            background-color: #1e293b;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #334155;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            padding: 24px;
            color: #ffffff;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .badge-env {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 12px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .content {
            padding: 24px;
        }
        .error-card {
            background-color: #0f172a;
            border-left: 4px solid #ef4444;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .error-message {
            font-size: 16px;
            font-weight: 700;
            color: #fca5a5;
            margin: 0 0 8px 0;
            word-break: break-word;
        }
        .error-class {
            font-size: 13px;
            color: #94a3b8;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            margin: 0;
            word-break: break-all;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #38bdf8;
            margin: 20px 0 10px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .info-grid {
            background-color: #0f172a;
            border-radius: 8px;
            padding: 14px 16px;
            border: 1px solid #334155;
            font-size: 13px;
        }
        .info-row {
            display: flex;
            padding: 6px 0;
            border-bottom: 1px solid #1e293b;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            width: 140px;
            flex-shrink: 0;
            color: #94a3b8;
            font-weight: 600;
        }
        .info-value {
            color: #f8fafc;
            word-break: break-all;
            flex-grow: 1;
        }
        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .code-block {
            background-color: #020617;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 14px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            color: #cbd5e1;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 380px;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            padding: 18px 24px;
            color: #64748b;
            font-size: 12px;
            border-top: 1px solid #334155;
            background-color: #172033;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 Error 500 en la Aplicación</h1>
            <div class="badge-env">{{ strtoupper($error['env']) }} &bull; {{ config('app.name', 'Whirlpool') }}</div>
        </div>

        <div class="content">
            <!-- Mensaje de Error Principal -->
            <div class="error-card">
                <div class="error-message">{{ $error['message'] }}</div>
                <div class="error-class">{{ $error['class'] }}</div>
                <div style="margin-top: 8px; font-size: 12px; color: #cbd5e1;" class="mono">
                    📍 <b>Archivo:</b> {{ $error['file'] }} : <b>Línea {{ $error['line'] }}</b>
                </div>
            </div>

            <!-- Detalles de la Petición -->
            <div class="section-title">🌐 Información de la Petición</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">URL / Ruta:</div>
                    <div class="info-value mono"><b>[{{ $error['method'] }}]</b> {{ $error['url'] }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha y Hora:</div>
                    <div class="info-value">{{ $error['occurred_at'] }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">IP del Cliente:</div>
                    <div class="info-value mono">{{ $error['ip'] }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Usuario:</div>
                    <div class="info-value">
                        @if($error['user'])
                            <b>{{ $error['user']['name'] }}</b> ({{ $error['user']['email'] }}) &bull; Rol: {{ $error['user']['role'] ?? 'N/A' }}
                        @else
                            <span style="color: #94a3b8;">Invitado / No autenticado</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">User Agent:</div>
                    <div class="info-value" style="font-size: 11px; color: #94a3b8;">{{ $error['user_agent'] }}</div>
                </div>
            </div>

            <!-- Parámetros de la petición si existen -->
            @if(!empty($error['inputs']))
                <div class="section-title">📦 Parámetros / Payload Recibido</div>
                <pre class="code-block">{{ json_encode($error['inputs'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            @endif

            <!-- Stack Trace -->
            <div class="section-title">📜 Stack Trace (Primeras líneas)</div>
            <pre class="code-block">{{ $error['trace'] }}</pre>
        </div>

        <div class="footer">
            Este es un correo automático generado por el fallback de excepciones de <b>{{ config('app.name', 'Whirlpool') }}</b> enviado a través de <b>Resend</b>.
        </div>
    </div>
</body>
</html>
