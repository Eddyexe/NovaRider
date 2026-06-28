<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #042D29;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #042D29;
            margin: 0;
            font-size: 22px;
        }
        .info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #042D29;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999;
            padding: 10px 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NovaRider</h1>
        <p>{{ $titulo }}</p>
    </div>

    <div class="info">
        <span><strong>Fecha de generación:</strong> {{ $fecha }}</span>
    </div>

    <table>
        <thead>
            @if($tipo === 'clientes')
                <tr>
                    <th>Nombre Cliente</th>
                    <th>CI / NIT</th>
                    <th>Teléfono</th>
                    <th class="text-center">Motos</th>
                </tr>
            @else
                <tr>
                    <th>Placa</th>
                    <th>Marca/Modelo</th>
                    <th>Cliente</th>
                    <th>Año/Color</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach($items as $item)
                @if($tipo === 'clientes')
                    <tr>
                        <td>{{ $item->primer_nombre }} {{ $item->apellido_paterno }}</td>
                        <td>{{ $item->ci }} {{ $item->nit ? '/ '.$item->nit : '' }}</td>
                        <td>{{ $item->telefono }}</td>
                        <td class="text-center">{{ $item->motocicletas_count }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $item->placa }}</td>
                        <td>{{ $item->marca }} {{ $item->modelo }}</td>
                        <td>{{ $item->cliente ? $item->cliente->primer_nombre . ' ' . $item->cliente->apellido_paterno : 'N/A' }}</td>
                        <td>{{ $item->anio }} / {{ $item->color }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        NovaRider - Sistema de Gestión de Taller de Motocicletas © {{ date('Y') }}
    </div>
</body>
</html>
