<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reporte de Movimientos</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header td {
            vertical-align: top;
        }

        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .report-info {
            text-align: right;
            color: #555;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background-color: #f0f0f0;
            border-bottom: 1px solid #ccc;
            text-align: left;
            padding: 5px;
            color: #333;
        }

        .table td {
            border-bottom: 1px solid #eee;
            padding: 5px;
            color: #555;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
        }

        .bg-ingreso {
            background-color: #28a745;
            color: white;
        }

        .bg-egreso {
            background-color: #dc3545;
            color: white;
        }

        .totals {
            width: 100%;
            margin-top: 20px;
        }

        .totals td {
            padding: 5px;
            text-align: right;
        }

        .total-label {
            font-weight: bold;
            color: #333;
        }

        .total-amount {
            font-weight: bold;
            font-size: 12px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <table class="header">
        <tr>
            <td width="60%">
                <div class="logo">Sistema Nande Iru</div>
                <div>Reporte General de Movimientos</div>
            </td>
            <td width="40%" class="report-info">
                Fecha: {{ date('d/m/Y H:i') }}<br>
                Generado por: {{ auth()->user()->name ?? 'Sistema' }}
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="15%">Fecha</th>
                <th width="15%">Cajero</th>
                <th width="10%">Tipo</th>
                <th width="40%">Concepto</th>
                <th width="15%" class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
                <tr>
                    <td>{{ $venta['id'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta['created_at'])->format('d/m/Y H:i') }}</td>
                    <td>{{ $venta['caja']['user']['name'] ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $venta['tipo'] == 'ingreso' ? 'bg-ingreso' : 'bg-egreso' }}">
                            {{ ucfirst($venta['tipo']) }}
                        </span>
                    </td>
                    <td>{{ $venta['concepto'] }}</td>
                    <td class="text-right">
                        Gs. {{ number_format($venta['monto'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        No hay movimientos para mostrar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td width="70%"></td>
            <td width="30%">
                <table width="100%">
                    <tr>
                        <td class="total-label">Total Ingresos:</td>
                        <td class="total-amount" style="color: #28a745;">
                            Gs. {{ number_format($ingresos, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="total-label">Total Egresos:</td>
                        <td class="total-amount" style="color: #dc3545;">
                            Gs. {{ number_format($egresos, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="total-label" style="border-top: 1px solid #ccc; padding-top: 5px;">Balance:</td>
                        <td class="total-amount" style="border-top: 1px solid #ccc; padding-top: 5px;">
                            Gs. {{ number_format($ingresos - $egresos, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Sistema Nande Iru - Reporte generado automáticamente
    </div>
</body>

</html>
