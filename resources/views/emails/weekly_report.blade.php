<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Semanal - Tu Pase al Mundial</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Main Card -->
                <table width="100%" max-width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td align="center"
                            style="background-color: #ffffff; padding: 40px 20px; border-bottom: 2px solid #f3f4f6;">
                            <img src="{{ config('app.url') }}/assets/9lives.png" alt="9lives Logo"
                                style="height: auto; max-height: 140px; display: block; margin-bottom: 15px;">
                            <h1
                                style="color: #111827; font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">
                                Reporte Semanal Oficial</h1>
                            <p style="color: #6b7280; font-size: 15px; margin: 8px 0 0 0; font-weight: 500;">Tu Pase Al
                                Mundial • Hisense & Toshiba</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #374151; font-size: 16px; line-height: 24px; margin: 0 0 20px 0;">Hola
                                Equipo,</p>
                            <p style="color: #4b5563; font-size: 15px; line-height: 24px; margin: 0 0 30px 0;">Adjunto
                                encontrarán el reporte consolidado de participantes, ventas y cupones generados
                                correspondiente al siguiente periodo de análisis:</p>

                            <!-- Date Range Card -->
                            <div
                                style="background-color: #f8fafc; border-left: 4px solid #E22319; padding: 15px 20px; border-radius: 0 8px 8px 0; margin-bottom: 30px;">
                                <p style="margin: 0 0 8px 0; color: #4b5563; font-size: 14px;"><strong>📅
                                        Desde:</strong> {{ $start->format('d') }} de
                                    {{ ucfirst($start->locale('es')->monthName) }}, {{ $start->format('H:i') }}</p>
                                <p style="margin: 0; color: #4b5563; font-size: 14px;"><strong>📅 Hasta:</strong>
                                    {{ $end->format('d') }} de {{ ucfirst($end->locale('es')->monthName) }},
                                    {{ $end->format('H:i') }}</p>
                            </div>

                            <h2
                                style="color: #111827; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
                                Resumen de Métricas</h2>

                            <!-- Metrics Grid -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td colspan="2" align="center" style="padding: 10px;">
                                        <div
                                            style="background-color: #ecfdf5; border: 1px solid #d1fae5; border-radius: 8px; padding: 25px 10px;">
                                            <p
                                                style="margin: 0; color: #065f46; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                                                Participantes Nuevos</p>
                                            <p
                                                style="margin: 10px 0 0 0; color: #064e3b; font-size: 36px; font-weight: 800;">
                                                {{ number_format($totals['valid_participants']) }}</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" align="center" style="padding: 10px;">
                                        <div
                                            style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px 10px;">
                                            <p
                                                style="margin: 0; color: #374151; font-size: 12px; text-transform: uppercase; font-weight: 700;">
                                                ventas hisense/toshiba</p>
                                            <p
                                                style="margin: 10px 0 0 0; color: #111827; font-size: 28px; font-weight: 800;">
                                                {{ number_format($totals['sales']) }}</p>
                                        </div>
                                    </td>
                                    <td width="50%" align="center" style="padding: 10px;">
                                        <div
                                            style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 20px 10px;">
                                            <p
                                                style="margin: 0; color: #92400e; font-size: 12px; text-transform: uppercase; font-weight: 700;">
                                                Cupones Emitidos</p>
                                            <p
                                                style="margin: 10px 0 0 0; color: #78350f; font-size: 28px; font-weight: 800;">
                                                {{ number_format($totals['coupons']) }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div
                                style="background-color: #eff6ff; border-radius: 8px; padding: 20px; text-align: center;">
                                <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 20px;">
                                    <strong>📎 Archivo Adjunto:</strong><br>
                                    La base de datos detallada en Excel (con 3 pestañas separadas) se encuentra segura y
                                    adjunta a este correo.
                                </p>
                            </div>

                            <p
                                style="color: #6b7280; font-size: 15px; margin: 30px 0 0 0; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                                Saludos cordiales,<br>
                                <strong style="color: #111827;">Admin 9lives</strong><br>
                                <span style="font-size: 13px;">Sistema Automático de Reportes</span>
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Footer -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; margin-top: 20px;">
                    <tr>
                        <td align="center">
                            <p style="color: #9ca3af; font-size: 12px; margin: 0; line-height: 18px;">
                                © {{ date('Y') }} 9lives & Dismac. Todos los derechos reservados.<br>
                                Este es un correo del sistema generado automáticamente. Por favor no responder a esta
                                dirección.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>