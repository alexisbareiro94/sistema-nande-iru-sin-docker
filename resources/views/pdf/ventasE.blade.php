<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Movimientos</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: table;
        }

        .header-content {
            display: table-cell;
            vertical-align: middle;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4a90e2;
            text-transform: uppercase;
        }

        .company-info {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        .report-title {
            text-align: right;
            display: table-cell;
            vertical-align: middle;
        }

        .report-title h1 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
            text-transform: uppercase;
        }

        .report-title p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #7f8c8d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #f4f6f7;
            color: #2c3e50;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 2px solid #ddd;
            text-transform: uppercase;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
            color: #555;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-ingreso {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-egreso {
            background-color: #ffebee;
            color: #c62828;
        }

        .totals {
            margin-top: 30px;
            border-top: 2px solid #4a90e2;
            padding-top: 15px;
        }

        .totals table {
            width: 40%;
            margin-left: auto;
            border: none;
        }

        .totals td {
            border: none;
            padding: 5px 10px;
            font-size: 12px;
        }

        .totals .final-total {
            font-weight: bold;
            font-size: 14px;
            color: #2c3e50;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">Sistema Nande Iru</div>
            <div class="company-info">Reporte generado automáticamente</div>
        </div>
        <div class="report-title">
            <h1>Reporte de Movimientos</h1>
            <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cajero</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Concepto</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $index => $venta)
                <tr>
                    <td>{{ $venta['id'] }}</td>
                    <td>{{ $venta['caja']['user']['name'] ?? 'N/A' }}</td>
                    <td>{{ isset($venta['created_at']) ? \Carbon\Carbon::parse($venta['created_at'])->format('d/m/Y H:i') : '' }}
                    </td>
                    <td>
                        <span class="badge {{ $venta['tipo'] == 'ingreso' ? 'badge-ingreso' : 'badge-egreso' }}">
                            {{ $venta['tipo'] }}
                        </span>
                    </td>
                    <td>{{ $venta['concepto'] }}</td>
                    <td class="text-right">Gs. {{ number_format($venta['monto'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Total Ingresos:</td>
                <td class="text-right" style="color: #2e7d32;">Gs. {{ number_format($ingresos, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Egresos:</td>
                <td class="text-right" style="color: #c62828;">Gs. {{ number_format($egresos, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="final-total">Balance:</td>
                <td class="text-right final-total">Gs. {{ number_format($ingresos - $egresos, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Este documento es un reporte generado por el sistema y no tiene validez fiscal. <br>
        &copy; {{ date('Y') }} Sistema Nande Iru.
    </div>
</body>

</html>
