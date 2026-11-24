<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de ventas</title>
    <style>
        body { font: 10pt DejaVu Sans, sans-serif; color: #17243b; }
        h1 { font-size: 18pt; margin: 0 0 12pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6pt; border-bottom: 1px solid #dce1e9; text-align: left; overflow-wrap: break-word; }
        th { background: #e9eef6; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .importe { text-align: right; white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Listado de ventas</h1>
    <table>
        <thead><tr>
            @foreach ($columnas as $columna)
                <th>{{ $columna }}</th>
            @endforeach
        </tr></thead>
        <tbody>
            @forelse ($filas as $fila)
                <tr>
                    @foreach ($fila as $indice => $valor)
                        <td @class(['importe' => $indice === 7])>{{ $indice === 7 ? number_format($valor, 2) : $valor }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="9">No hay ventas que coincidan con los filtros.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
