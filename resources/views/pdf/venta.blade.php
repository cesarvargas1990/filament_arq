<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Venta</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Factura de Venta #{{ $venta->id }}</h2>
    <p><strong>Vendedor:</strong> {{ $venta->user->name }}</p>
    <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</p>
    <p><strong>Fecha:</strong> {{ $venta->created_at->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td>{{ $detalle->product->nombre_producto }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>${{ number_format($detalle->product->precio, 2) }}</td>
                <td>${{ number_format($detalle->cantidad * $detalle->product->precio, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total Venta: ${{ number_format($venta->total, 2) }}</h3>
</body>
</html>
