<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lote: {{ $lote->nombre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 20px;
        }
        .header p {
            margin: 3px 0;
            font-size: 12px;
        }
        .grid-container {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .grid-row {
            display: table-row;
        }
        .ficha-card {
            display: table-cell;
            width: 25%;
            border: 1px solid #333;
            padding: 8px;
            vertical-align: top;
            page-break-inside: avoid;
        }
        .ficha-card .perfil {
            font-weight: bold;
            font-size: 11px;
            color: #0066cc;
            margin-bottom: 5px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        .ficha-card .usuario, .ficha-card .clave {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            margin: 3px 0;
        }
        .ficha-card .label {
            font-weight: bold;
            display: inline-block;
            width: 45px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $lote->nombre }}</h1>
        <p><strong>Perfil:</strong> {{ $lote->perfil->nombre }} | <strong>Total Fichas:</strong> {{ $fichas->count() }}</p>
        @if($lote->nas)
        <p><strong>NAS:</strong> {{ $lote->nas->nombre }}</p>
        @endif
    </div>
    
    <div class="grid-container">
        @foreach($fichas->chunk(4) as $rowIndex => $row)
        <div class="grid-row">
            @foreach($row as $ficha)
            <div class="ficha-card">
                <div class="perfil">{{ $ficha['perfil'] }}</div>
                <div class="usuario"><span class="label">Usuario:</span> {{ $ficha['usuario'] }}</div>
                <div class="clave"><span class="label">Clave:</span> {{ $ficha['clave'] }}</div>
            </div>
            @endforeach
            @if($row->count() < 4)
                @for($i = $row->count(); $i < 4; $i++)
                <div class="ficha-card" style="border: none;"></div>
                @endfor
            @endif
        </div>
        @endforeach
    </div>
</body>
</html>
