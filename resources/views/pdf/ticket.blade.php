<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte {{ $ticket->folio }}</title>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 13px; 
            color: #333; 
            margin: 0;
            padding: 10px;
        }
        .header { 
            text-align: center; 
            border-bottom: 3px solid #054c31; 
            padding-bottom: 15px; 
            margin-bottom: 15px; 
        }
        .logo { 
            max-width: 130px; 
            margin-bottom: 10px;
        }
        .title { 
            color: #054c31; 
            font-size: 20px; 
            text-transform: uppercase; 
            margin: 0; 
            font-weight: bold;
        }
        .subtitle { 
            color: #e7bc13; 
            font-size: 14px; 
            font-weight: bold; 
            margin-top: 5px;
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        .table th, .table td { 
            border: 1px solid #e0e0e0; 
            padding: 10px; 
            text-align: left; 
        }
        .table th { 
            background-color: #fafafa; 
            color: #054c31; 
            width: 35%; 
            font-weight: bold;
        }
        .description-box { 
            margin-top: 20px; 
            border: 1px solid #e0e0e0; 
            padding: 12px; 
            background-color: #fafafa; 
            border-left: 5px solid #e7bc13;
        }
        .description-box h4 {
            color: #054c31; 
            margin-top: 0;
            margin-bottom: 8px;
        }
        .footer { 
            text-align: center; 
            font-size: 10px; 
            color: #777; 
            margin-top: 30px;
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
            <td style="font-weight: bold; color: #054c31;">{{ $ticket->folio }}</td>
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

    @php
        $groupName = $ticket->ticket_group;
        $detail = match ($groupName) {
            'SGC' => $ticket->ticketSgcDetail,
            'GENERO' => $ticket->ticketGenderDetail,
            'INFRAESTRUCTURA' => $ticket->ticketInfraDetail,
            default => null,
        };
        
        $fields = [];
        if ($detail) {
            $form = \App\Models\DynamicForm::where('name', $groupName)->first();
            if ($form) {
                $fields = \App\Models\DynamicFormField::whereHas('step', function ($q) use ($form) {
                        $q->where('dynamic_form_id', $form->id);
                    })
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }
        }
    @endphp

    @if($detail && count($fields) > 0)
        <h4 style="color: #054c31; margin-top: 20px; border-bottom: 2px solid #e7bc13; padding-bottom: 5px; text-transform: uppercase;">Detalles del Reporte</h4>
        <table class="table" style="margin-top: 5px;">
            @foreach($fields as $field)
                @php
                    $name = $field->name;
                    $value = null;
                    
                    // Columnas legadas de la BD
                    $isLegacy = false;
                    $sgcColumns = ['reported_person_name', 'user_type', 'department_id', 'subdepartment_id', 'academic_division_id', 'educational_program_id', 'classification', 'description'];
                    $generoColumns = ['reported_person_name', 'reported_person_type', 'campus_id', 'department_id', 'subdepartment_id', 'academic_division_id', 'educational_program_id', 'reported_person_details', 'manifestation_type', 'chronological_narrative', 'extended_narrative', 'has_evidence', 'witnesses_details', 'needs_psychological_support', 'communicated_to', 'communication_results'];
                    $infraColumns = ['campus_id', 'building_id', 'location_id', 'issue_type', 'missing_supplies', 'description'];

                    if ($groupName === 'SGC' && in_array($name, $sgcColumns)) $isLegacy = true;
                    if ($groupName === 'GENERO' && in_array($name, $generoColumns)) $isLegacy = true;
                    if ($groupName === 'INFRAESTRUCTURA' && in_array($name, $infraColumns)) $isLegacy = true;

                    if ($isLegacy) {
                        if (str_ends_with($name, '_id')) {
                            $relName = match ($name) {
                                'campus_id' => 'campus',
                                'building_id' => 'building',
                                'location_id' => 'location',
                                'department_id' => 'department',
                                'subdepartment_id' => 'subdepartment',
                                'academic_division_id' => 'academicDivision',
                                'educational_program_id' => 'educationalProgram',
                                default => null,
                            };
                            $value = $relName && $detail->$relName ? $detail->$relName->name : null;
                        } else {
                            $value = $detail->$name;
                        }
                    } else {
                        // Campo dinámico extra
                        $extra = $detail->extra_attributes;
                        $value = $extra[$name] ?? null;
                    }

                    // Formateo de valores
                    if (is_bool($value)) {
                        $value = $value ? 'Sí' : 'No';
                    } elseif (is_array($value)) {
                        if ($field->options) {
                            $value = collect($value)->map(fn($v) => $field->options[$v] ?? $v)->implode(', ');
                        } else {
                            $value = implode(', ', $value);
                        }
                    } elseif ($field->type === 'select' && $field->options) {
                        $value = $field->options[$value] ?? $value;
                    } elseif ($field->type === 'select_user_type') {
                        $value = match($value) {
                            'administrativo' => 'Personal Administrativo',
                            'academico'      => 'Personal Académico',
                            'estudiante'     => 'Estudiante',
                            default          => $value,
                        };
                    }
                @endphp
                @if($value !== null && $value !== '')
                    <tr>
                        <th>{{ $field->label }}</th>
                        <td>{{ $value }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
    @endif

    <div class="footer">
        Documento oficial generado automáticamente por la Plataforma UAEQROO.<br>
        Este comprobante ampara el levantamiento del reporte bajo las reglas institucionales vigentes.
    </div>

</body>
</html>
