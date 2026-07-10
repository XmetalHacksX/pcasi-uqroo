<?php

namespace Database\Seeders;

use App\Models\DynamicForm;
use App\Models\DynamicFormStep;
use App\Models\DynamicFormField;
use Illuminate\Database\Seeder;

class DynamicFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. FORM SGC
        $sgcForm = DynamicForm::updateOrCreate(
            ['name' => 'SGC'],
            ['title' => 'Queja, Sugerencia o Felicitación (SGC)', 'description' => 'Formulario para registrar quejas, sugerencias y felicitaciones en el Sistema de Gestión de Calidad.']
        );

        $sgcStep = DynamicFormStep::updateOrCreate(
            ['dynamic_form_id' => $sgcForm->id, 'title' => 'Detalles del Reporte'],
            ['description' => 'Completa la información de tu reporte', 'sort_order' => 10]
        );

        $sgcFields = [
            [
                'name' => 'reported_person_name',
                'label' => 'Nombre de la persona involucrada (Opcional)',
                'type' => 'text',
                'options' => null,
                'placeholder' => 'Ej. Juan Pérez',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'user_type',
                'label' => 'Tipo de Usuario',
                'type' => 'select_user_type',
                'options' => null,
                'placeholder' => 'Selecciona una opción...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'temp_campus_id',
                'label' => 'Campus',
                'type' => 'select_campus',
                'options' => null,
                'placeholder' => 'Selecciona un campus...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'department_id',
                'label' => 'Dirección General',
                'type' => 'select_department',
                'options' => null,
                'placeholder' => 'Selecciona la dirección...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'subdepartment_id',
                'label' => 'Departamento / Oficina',
                'type' => 'select_subdepartment',
                'options' => null,
                'placeholder' => 'Selecciona la oficina...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 50,
            ],
            [
                'name' => 'academic_division_id',
                'label' => 'División Académica',
                'type' => 'select_academic_division',
                'options' => null,
                'placeholder' => 'Selecciona la división...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 60,
            ],
            [
                'name' => 'educational_program_id',
                'label' => 'Carrera / Programa Educativo',
                'type' => 'select_educational_program',
                'options' => null,
                'placeholder' => 'Selecciona la carrera...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 70,
            ],
            [
                'name' => 'classification',
                'label' => 'Los hechos corresponden a una:',
                'type' => 'select',
                'options' => [
                    'Queja' => 'Queja',
                    'Sugerencia' => 'Sugerencia',
                    'Felicitación' => 'Felicitación',
                ],
                'placeholder' => 'Selecciona una clasificación...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 80,
            ],
            [
                'name' => 'description',
                'label' => 'Descripción del Asunto',
                'type' => 'textarea',
                'options' => null,
                'placeholder' => 'Describe detalladamente los hechos...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 90,
            ]
        ];

        foreach ($sgcFields as $field) {
            DynamicFormField::updateOrCreate(
                ['dynamic_form_step_id' => $sgcStep->id, 'name' => $field['name']],
                $field
            );
        }

        // 2. FORM GENERO
        $generoForm = DynamicForm::updateOrCreate(
            ['name' => 'GENERO'],
            ['title' => 'Reporte de Violencia de Género o Discriminación', 'description' => 'Formulario para levantar reportes relacionados con acoso, hostigamiento, discriminación y violencia de género.']
        );

        $generoStep = DynamicFormStep::updateOrCreate(
            ['dynamic_form_id' => $generoForm->id, 'title' => 'Detalles del Reporte'],
            ['description' => 'Completa la información de tu reporte', 'sort_order' => 10]
        );

        $generoFields = [
            [
                'name' => 'reported_person_name',
                'label' => 'Nombre de la persona contra quien se presenta la queja',
                'type' => 'text',
                'options' => null,
                'placeholder' => 'Ej. Nombre Completo',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'reported_person_type',
                'label' => 'Tipo de usuario de la persona responsable',
                'type' => 'select',
                'options' => [
                    'academico' => 'Estudiante / Docente',
                    'administrativo' => 'Personal Administrativo',
                    'Externo' => 'Externo',
                ],
                'placeholder' => 'Selecciona una opción...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'campus_id',
                'label' => 'Campus de Adscripción',
                'type' => 'select_campus',
                'options' => null,
                'placeholder' => 'Selecciona un campus...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'department_id',
                'label' => 'Área / Unidad Académica / Dirección General',
                'type' => 'select_department',
                'options' => null,
                'placeholder' => 'Selecciona...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'subdepartment_id',
                'label' => 'Oficina / Subdepartamento (Opcional)',
                'type' => 'select_subdepartment',
                'options' => null,
                'placeholder' => 'Selecciona...',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 50,
            ],
            [
                'name' => 'academic_division_id',
                'label' => 'División Académica de Adscripción',
                'type' => 'select_academic_division',
                'options' => null,
                'placeholder' => 'Selecciona...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 60,
            ],
            [
                'name' => 'educational_program_id',
                'label' => 'Programa Académico de Adscripción (Carrera)',
                'type' => 'select_educational_program',
                'options' => null,
                'placeholder' => 'Selecciona...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 70,
            ],
            [
                'name' => 'reported_person_details',
                'label' => 'Datos institucionales adicionales (Cargo, Oficina, etc.)',
                'type' => 'text',
                'options' => null,
                'placeholder' => 'Escriba el cargo o detalles específicos...',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 80,
            ],
            [
                'name' => 'manifestation_type',
                'label' => '¿De qué forma se manifestó el hecho?',
                'type' => 'select',
                'options' => [
                    'Violencia de género' => 'Violencia de género',
                    'Acoso' => 'Acoso / Hostigamiento',
                    'Discriminación' => 'Discriminación',
                ],
                'placeholder' => 'Selecciona...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 90,
            ],
            [
                'name' => 'chronological_narrative',
                'label' => 'Narración cronológica de los hechos (tiempo, modo y lugar)',
                'type' => 'textarea',
                'options' => null,
                'placeholder' => 'Describe lo sucedido con fechas, horas y lugares...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 100,
            ],
            [
                'name' => 'has_evidence',
                'label' => '¿Cuenta con pruebas o evidencias?',
                'type' => 'toggle',
                'options' => null,
                'placeholder' => null,
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 110,
            ],
            [
                'name' => 'witnesses',
                'label' => '¿Hubo testigos?',
                'type' => 'select',
                'options' => [
                    'si' => 'Sí hubo testigos',
                    'no' => 'Nadie los presenció',
                ],
                'placeholder' => 'Selecciona...',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 120,
            ],
            [
                'name' => 'witnesses_details',
                'label' => 'Nombre y datos de contacto de los testigos',
                'type' => 'textarea',
                'options' => null,
                'placeholder' => 'Ej. Nombre completo y teléfono/correo...',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 130,
            ],
            [
                'name' => 'needs_psychological_support',
                'label' => '¿Considera necesario acompañamiento profesional?',
                'type' => 'toggle',
                'options' => null,
                'placeholder' => null,
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 140,
            ],
            [
                'name' => 'has_communicated',
                'label' => '¿Has reportado este incidente previamente a alguna autoridad de la universidad?',
                'type' => 'toggle',
                'options' => null,
                'placeholder' => null,
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 150,
            ],
            [
                'name' => 'communicated_to',
                'label' => 'Comuniqué estos hechos a:',
                'type' => 'select_multiple',
                'options' => [
                    'Tutor(a)' => 'Tutor(a)',
                    'Docente' => 'Docente',
                    'Jefe(a) de Departamento' => 'Jefe(a) de Departamento',
                    'Director(a) de División' => 'Director(a) de División',
                    'Recursos Humanos' => 'Recursos Humanos',
                    'Abogado General' => 'Abogado General',
                    'Otro' => 'Otro',
                ],
                'placeholder' => 'Selecciona uno o varios...',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 160,
            ],
            [
                'name' => 'communication_results',
                'label' => 'Resultado de la comunicación (opcional)',
                'type' => 'textarea',
                'options' => null,
                'placeholder' => 'Describe qué respuesta obtuviste...',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 170,
            ]
        ];

        foreach ($generoFields as $field) {
            DynamicFormField::updateOrCreate(
                ['dynamic_form_step_id' => $generoStep->id, 'name' => $field['name']],
                $field
            );
        }

        // 3. FORM INFRAESTRUCTURA
        $infraForm = DynamicForm::updateOrCreate(
            ['name' => 'INFRAESTRUCTURA'],
            ['title' => 'Reporte de Infraestructura', 'description' => 'Formulario para reportar fallas de red, limpieza, mantenimiento y otros problemas en las instalaciones físicas de la universidad.']
        );

        $infraStep = DynamicFormStep::updateOrCreate(
            ['dynamic_form_id' => $infraForm->id, 'title' => 'Detalles del Reporte'],
            ['description' => 'Completa la información de tu reporte', 'sort_order' => 10]
        );

        $infraFields = [
            [
                'name' => 'campus_id',
                'label' => 'Campus',
                'type' => 'select_campus',
                'options' => null,
                'placeholder' => 'Selecciona un campus...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'building_id',
                'label' => 'Edificio',
                'type' => 'select_building',
                'options' => null,
                'placeholder' => 'Selecciona un edificio...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'location_id',
                'label' => 'Ubicación específica (aula, baño, pasillo...)',
                'type' => 'select_location',
                'options' => null,
                'placeholder' => 'Selecciona una ubicación...',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 30,
            ],
            [
                'name' => 'issue_type',
                'label' => 'Tipo de falla',
                'type' => 'select',
                'options' => [
                    'Falla Red' => 'Falla de Red / Internet',
                    'Limpieza' => 'Limpieza',
                    'Mantenimiento' => 'Mantenimiento General',
                    'Electricidad' => 'Electricidad / Iluminación',
                    'Otro' => 'Otro',
                ],
                'placeholder' => 'Selecciona el tipo de falla...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'missing_supplies',
                'label' => 'Insumos Faltantes (Opcional)',
                'type' => 'select_multiple',
                'options' => [
                    'jabón' => 'Jabón',
                    'papel' => 'Papel Higiénico',
                    'toallas' => 'Toallas de papel',
                ],
                'placeholder' => 'Selecciona...',
                'helper_text' => null,
                'is_required' => false,
                'sort_order' => 50,
            ],
            [
                'name' => 'description',
                'label' => 'Descripción detallada de la falla',
                'type' => 'textarea',
                'options' => null,
                'placeholder' => 'Describe detalladamente el problema y su ubicación exacta...',
                'helper_text' => null,
                'is_required' => true,
                'sort_order' => 60,
            ]
        ];

        foreach ($infraFields as $field) {
            DynamicFormField::updateOrCreate(
                ['dynamic_form_step_id' => $infraStep->id, 'name' => $field['name']],
                $field
            );
        }
    }
}
