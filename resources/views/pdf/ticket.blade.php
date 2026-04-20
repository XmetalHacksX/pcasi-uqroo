<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte {{ $ticket->folio }}</title>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 14px; 
            color: #333; 
            margin: 0;
            padding: 20px;
        }
        .header { 
            text-align: center; 
            border-bottom: 3px solid #054c31; 
            padding-bottom: 20px; 
            margin-bottom: 20px; 
        }
        .logo { 
            max-width: 150px; 
            margin-bottom: 15px;
        }
        .title { 
            color: #054c31; 
            font-size: 22px; 
            text-transform: uppercase; 
            margin: 0; 
            font-weight: bold;
        }
        .subtitle { 
            color: #e7bc13; 
            font-size: 16px; 
            font-weight: bold; 
            margin-top: 5px;
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        .table th, .table td { 
            border: 1px solid #e0e0e0; 
            padding: 12px; 
            text-align: left; 
        }
        .table th { 
            background-color: #fafafa; 
            color: #054c31; 
            width: 35%; 
        }
        .description-box { 
            margin-top: 30px; 
            border: 1px solid #e0e0e0; 
            padding: 15px; 
            background-color: #fafafa; 
            border-left: 5px solid #e7bc13;
        }
        .description-box h4 {
            color: #054c31; 
            margin-top: 0;
            margin-bottom: 10px;
        }
        .footer { 
            text-align: center; 
            font-size: 10px; 
            color: #777; 
            position: absolute;
            bottom: 30px;
            width: 100%;
            border-top: 1px solid #e0e0e0; 
            padding-top: 10px; 
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/Escudo UAEQROO Oficial-01.png') }}" class="logo" alt="Logo UAEQROO">
        <div class="title">Sistema Institucional de Mesa de Ayuda</div>
        <div class="subtitle">Comprobante de Reporte</div>
    </div>

    <table class="table">
        <tr>
            <th>Folio Institucional</th>
            <td style="font-weight: bold;">{{ $ticket->folio }}</td>
        </tr>
        <tr>
            <th>Fecha del Reporte</th>
            <td>{{ $ticket->created_at->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <th>Reportado por</th>
            <td>{{ $ticket->reporter?->name ?? 'Usuario del Sistema' }}</td>
        </tr>
        <tr>
            <th>Área de Atención</th>
            <td>{{ $ticket->ticket_group }}</td>
        </tr>
        <tr>
            <th>Estatus Actual</th>
            <td>{{ $ticket->status?->name ?? 'En proceso' }}</td>
        </tr>
    </table>

    <div class="description-box">
        <h4>Resumen de los Hechos</h4>
        @php
            $desc = 'Descripción no disponible o no aplica a este grupo.';
            if ($ticket->ticket_group === 'SGC') {
                $desc = $ticket->ticketSgcDetail?->description;
            } elseif ($ticket->ticket_group === 'INFRAESTRUCTURA') {
                $desc = $ticket->ticketInfraDetail?->description;
            } elseif ($ticket->ticket_group === 'GENERO') {
                $desc = $ticket->ticketGenderDetail?->chronological_narrative;
            }
        @endphp
        <p style="margin: 0; line-height: 1.5;">{{ $desc ?? 'No se proporcionó una narrativa.' }}</p>
    </div>

    <div class="footer">
        Documento oficial generado automáticamente por la Plataforma UAEQROO.<br>
        Este comprobante ampara el levantamiento del reporte bajo las reglas institucionales vigentes.
    </div>

</body>
</html>
