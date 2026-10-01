<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario - Consul</title>
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
                                Reporte Diario Consul</h1>
                            <p style="color: #6b7280; font-size: 15px; margin: 8px 0 0 0; font-weight: 500;">
                                PROMO CONSUL
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #374151; font-size: 16px; line-height: 24px; margin: 0 0 20px 0;">Hola
                                Equipo,</p>
                            <p style="color: #4b5563; font-size: 15px; line-height: 24px; margin: 0 0 30px 0;">
                                Adjunto encontrarán el reporte de gestión diario de **Consul** correspondiente al siguiente periodo:
                            </p>

                            <!-- Date Range Card -->
                            <div
                                style="background-color: #f8fafc; border-left: 4px solid #2563eb; padding: 15px 20px; border-radius: 0 8px 8px 0; margin-bottom: 30px;">
                                <p style="margin: 0; color: #4b5563; font-size: 14px;">
                                    <strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($dateStart)->format('d/m/Y') }}
                                </p>
                            </div>

                            <h2
                                style="color: #111827; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
                                Resumen de Métricas</h2>

                            <!-- Metrics Grid -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px; border-collapse: separate; border-spacing: 0;">
                                <tr>
                                    <td align="center" style="padding: 10px;">
                                        <div
                                            style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; padding: 25px 10px; width: 100%; box-sizing: border-box;">
                                            <p
                                                style="margin: 0; color: #1e40af; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                                                Total Participaciones</p>
                                            <p
                                                style="margin: 10px 0 0 0; color: #1e3a8a; font-size: 36px; font-weight: 800;">
                                                {{ number_format($totalForms) }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td width="33.33%" align="center" style="padding: 5px;">
                                        <div
                                            style="background-color: #ecfdf5; border: 1px solid #d1fae5; border-radius: 8px; padding: 15px 5px;">
                                            <p
                                                style="margin: 0; color: #065f46; font-size: 10px; text-transform: uppercase; font-weight: 700;">
                                                Entregados</p>
                                            <p
                                                style="margin: 8px 0 0 0; color: #064e3b; font-size: 20px; font-weight: 800;">
                                                {{ number_format($totalEntregados) }}</p>
                                        </div>
                                    </td>
                                    <td width="33.33%" align="center" style="padding: 5px;">
                                        <div
                                            style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 15px 5px;">
                                            <p
                                                style="margin: 0; color: #92400e; font-size: 10px; text-transform: uppercase; font-weight: 700;">
                                                Pendientes</p>
                                            <p
                                                style="margin: 8px 0 0 0; color: #78350f; font-size: 20px; font-weight: 800;">
                                                {{ number_format($totalPendientes) }}</p>
                                        </div>
                                    </td>
                                    <td width="33.33%" align="center" style="padding: 5px;">
                                        <div
                                            style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 15px 5px;">
                                            <p
                                                style="margin: 0; color: #991b1b; font-size: 10px; text-transform: uppercase; font-weight: 700;">
                                                Invalidados</p>
                                            <p
                                                style="margin: 8px 0 0 0; color: #7f1d1d; font-size: 20px; font-weight: 800;">
                                                {{ number_format($totalInvalidos) }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Detailed Sections -->
                            <h3 style="color: #111827; font-size: 16px; font-weight: 600; margin: 20px 0 15px 0;">🎁 Premios Entregados</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; border-collapse: separate;">
                                <thead>
                                    <tr style="background-color: #f9fafb;">
                                        <th align="left" style="padding: 12px 15px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;">Premio</th>
                                        <th align="right" style="padding: 12px 15px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($porPremio as $row)
                                        <tr>
                                            <td style="padding: 12px 15px; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6;">{{ $row->prize->name ?? 'Desconocido' }}</td>
                                            <td align="right" style="padding: 12px 15px; font-size: 14px; font-weight: 700; color: #059669; border-bottom: 1px solid #f3f4f6;">{{ number_format($row->total) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" style="padding: 20px; text-align: center; font-size: 13px; color: #9ca3af; font-style: italic;">Sin datos registrados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <h3 style="color: #111827; font-size: 16px; font-weight: 600; margin: 30px 0 15px 0;">📍 Entregas por Sucursal</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; border-collapse: separate;">
                                <thead>
                                    <tr style="background-color: #f9fafb;">
                                        <th align="left" style="padding: 12px 15px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;">Sucursal</th>
                                        <th align="right" style="padding: 12px 15px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;">Entregas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($porSucursal as $row)
                                        <tr>
                                            <td style="padding: 12px 15px; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6;">
                                                {{ $row->branch->name ?? 'Desconocido' }} 
                                                <div style="font-size: 11px; color: #9ca3af;">{{ $row->branch->city ?? '-' }}</div>
                                            </td>
                                            <td align="right" style="padding: 12px 15px; font-size: 14px; font-weight: 700; color: #2563eb; border-bottom: 1px solid #f3f4f6;">{{ number_format($row->total) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" style="padding: 20px; text-align: center; font-size: 13px; color: #9ca3af; font-style: italic;">Sin datos registrados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <h3 style="color: #111827; font-size: 16px; font-weight: 600; margin: 30px 0 15px 0;">🌍 Distribución por Ciudad</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
                                @forelse($porCiudad as $row)
                                    @php
                                        $percentage = $totalForms > 0 ? ($row->total / $totalForms) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td style="padding: 10px 0;">
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 6px;">
                                                <tr>
                                                    <td align="left" style="font-size: 13px; font-weight: 600; color: #374151;">{{ $row->city ?: 'Sin Ciudad' }}</td>
                                                    <td align="right" style="font-size: 13px; font-weight: 600; color: #374151;">{{ number_format($row->total) }}</td>
                                                </tr>
                                            </table>
                                            <div style="height: 8px; background-color: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                                <div style="height: 100%; background-color: #f59e0b; width: {{ $percentage }}%;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td style="padding: 20px; text-align: center; font-size: 13px; color: #9ca3af; font-style: italic;">Sin datos registrados</td>
                                    </tr>
                                @endforelse
                            </table>

                            <div
                                style="background-color: #eff6ff; border-radius: 8px; padding: 20px; text-align: center; margin-top: 30px;">
                                <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 20px;">
                                    <strong>📎 Archivo Adjunto:</strong><br>
                                    El detalle completo de participaciones se adjunta en formato Excel para su revisión.
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
