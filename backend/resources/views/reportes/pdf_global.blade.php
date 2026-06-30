<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #042D29; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #042D29; margin: 0; }
        .summary-box { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .summary-title { font-weight: bold; color: #042D29; margin-bottom: 10px; border-bottom: 1px solid #eee; }
        .stat-row { display: block; margin-bottom: 5px; }
        .stat-label { font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; }
        .section-title { background: #042D29; color: white; padding: 5px 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NovaRider</h1>
        <h2>{{ $titulo }}</h2>
        <p>Fecha: {{ $fecha }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-title">Resumen Ejecutivo</div>
        <div class="stat-row"><span class="stat-label">Total Clientes:</span> {{ $stats['clientes'] }}</div>
        <div class="stat-row"><span class="stat-label">Total Motocicletas:</span> {{ $stats['motos'] }}</div>
        <div class="stat-row"><span class="stat-label">Personal Activo:</span> {{ $stats['usuarios'] }}</div>
        <div class="stat-row"><span class="stat-label">Productos en Inventario:</span> {{ $stats['productos'] }}</div>
    </div>

    <div class="summary-box" style="border-top: 4px solid #741102;">
        <div class="summary-title">Indicadores Mensuales</div>
        <div class="stat-row"><span class="stat-label">Ventas realizadas este mes:</span> {{ $stats['ventas_mes'] }}</div>
        <div class="stat-row"><span class="stat-label">Ingresos Totales Acumulados:</span> {{ number_format($stats['ingresos_total'], 2) }} BOB</div>
    </div>

    <div style="margin-top: 50px;">
        <p>Este reporte proporciona una visión general del estado actual del sistema NovaRider. Para detalles específicos, por favor genere los reportes individuales por módulo.</p>
    </div>

    <div class="footer">
        NovaRider - Sistema de Gestión de Taller de Motocicletas © {{ date('Y') }}
    </div>
</body>
</html>
