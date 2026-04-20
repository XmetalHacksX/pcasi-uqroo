<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campus;
use App\Models\AcademicDivision;
use App\Models\EducationalProgram;
use App\Models\Department;
use App\Models\Subdepartment;
use App\Models\Building;
use App\Models\Location;
use App\Models\Status;

class UqrooCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Estados del Ticket
        Status::insert([
            ['id' => 1, 'name' => 'Nuevo', 'color' => 'danger', 'is_closed' => 0],
            ['id' => 2, 'name' => 'Asignado', 'color' => 'warning', 'is_closed' => 0],
            ['id' => 3, 'name' => 'En Proceso', 'color' => 'info', 'is_closed' => 0],
            ['id' => 4, 'name' => 'Resuelto', 'color' => 'success', 'is_closed' => 1],
            ['id' => 5, 'name' => 'Cancelado', 'color' => 'gray', 'is_closed' => 1],
        ]);

        // 2. Campus Reales
        Campus::insert([
            ['id' => 1, 'name' => 'Campus Chetumal Bahía'],
            ['id' => 2, 'name' => 'Campus Chetumal Salud'],
            ['id' => 3, 'name' => 'Campus Playa del Carmen'],
            ['id' => 4, 'name' => 'Campus Cozumel'],
            ['id' => 5, 'name' => 'Campus Cancún'],
            ['id' => 6, 'name' => 'Campus Felipe Carrillo Puerto'],
        ]);

        // 3. Direcciones Generales / Departamentos (Asignados a Chetumal Bahía)
        Department::insert([
            ['id' => 1, 'campus_id' => 1, 'name' => 'Rectoría'],
            ['id' => 2, 'campus_id' => 1, 'name' => 'Secretaría General'],
            ['id' => 3, 'campus_id' => 1, 'name' => 'Dirección General de Servicios Estudiantiles'],
            ['id' => 4, 'campus_id' => 1, 'name' => 'Dirección General de Asuntos Jurídicos'],
            ['id' => 5, 'campus_id' => 1, 'name' => 'Dirección General de Desarrollo Académico'],
            ['id' => 6, 'campus_id' => 1, 'name' => 'Dirección General de Investigación, Posgrado y Vinculación'],
            ['id' => 7, 'campus_id' => 1, 'name' => 'Dirección General de TI'],
            ['id' => 8, 'campus_id' => 1, 'name' => 'Dirección General de Imagen Institucional'],
            ['id' => 9, 'campus_id' => 1, 'name' => 'Dirección General de Planeación'],
            ['id' => 10, 'campus_id' => 1, 'name' => 'Dirección General de Administración y Finanzas'],
            ['id' => 11, 'campus_id' => 1, 'name' => 'Auditoría Interna'],
        ]);

        // 4. Divisiones Académicas Reales (Respetando IDs originales)
        AcademicDivision::insert([
            ['id' => 1, 'campus_id' => 1, 'name' => 'División de Ciencias, Ingeniería y Tecnología', 'acronym' => 'DCIT'],
            ['id' => 2, 'campus_id' => 1, 'name' => 'División de Ciencias Políticas, Económicas y Administrativas', 'acronym' => 'DCPEA'],
            ['id' => 3, 'campus_id' => 2, 'name' => 'División de Ciencias de la Salud', 'acronym' => 'DCS'],
            ['id' => 4, 'campus_id' => 4, 'name' => 'División de Ciencias Multidisciplinarias Cozumel', 'acronym' => 'DCMCOZ'],
            ['id' => 8, 'campus_id' => 3, 'name' => 'División de Ciencias Multidisciplinarias Playa del Carmen', 'acronym' => 'DCMPC'],
            ['id' => 9, 'campus_id' => 5, 'name' => 'División de Ciencias Multidisciplinarias Cancún', 'acronym' => 'DCMC'],
            ['id' => 10, 'campus_id' => 6, 'name' => 'División de Ciencias Sociales y Humanidades', 'acronym' => 'DCSH'],
            ['id' => 11, 'campus_id' => 6, 'name' => 'División de Ciencias, Ingeniería y Tecnología', 'acronym' => 'DCIT'],
            ['id' => 12, 'campus_id' => 1, 'name' => 'División de Ciencias Sociales y Humanidades', 'acronym' => 'DCSH'],
        ]);

        // 5. Programas Educativos Reales
        EducationalProgram::insert([
            ['id' => 1, 'academic_division_id' => 11, 'name' => 'Ingeniería Ambiental'],
            ['id' => 2, 'academic_division_id' => 11, 'name' => 'Ingeniería en Redes y Ciberseguridad'],
            ['id' => 3, 'academic_division_id' => 11, 'name' => 'Ingeniería en Sistemas de Energía'],
            ['id' => 4, 'academic_division_id' => 11, 'name' => 'Manejo de Recursos Naturales'],
            ['id' => 5, 'academic_division_id' => 11, 'name' => 'Ingeniería en Inteligencia Artificial Aplicada (mixta)'],
            ['id' => 6, 'academic_division_id' => 2, 'name' => 'Economía y Finanzas'],
            ['id' => 7, 'academic_division_id' => 2, 'name' => 'Gestión del Turismo Alternativo'],
            ['id' => 8, 'academic_division_id' => 2, 'name' => 'Gobierno y Gestión Pública'],
            ['id' => 9, 'academic_division_id' => 2, 'name' => 'Mercadotecnia y Negocios'],
            ['id' => 10, 'academic_division_id' => 2, 'name' => 'Relaciones Internacionales'],
            ['id' => 11, 'academic_division_id' => 12, 'name' => 'Antropología Social'],
            ['id' => 12, 'academic_division_id' => 12, 'name' => 'Derecho'],
            ['id' => 13, 'academic_division_id' => 12, 'name' => 'Humanidades'],
            ['id' => 14, 'academic_division_id' => 12, 'name' => 'Lengua Inglesa'],
            ['id' => 15, 'academic_division_id' => 12, 'name' => 'Seguridad Pública y Criminalística'],
            ['id' => 16, 'academic_division_id' => 12, 'name' => 'Enseñanza de la Lengua Maya (mixta)'],
            ['id' => 17, 'academic_division_id' => 3, 'name' => 'Medicina'],
            ['id' => 18, 'academic_division_id' => 3, 'name' => 'Enfermería'],
            ['id' => 19, 'academic_division_id' => 3, 'name' => 'Farmacia'],
            ['id' => 20, 'academic_division_id' => 3, 'name' => 'Nutrición'],
            ['id' => 21, 'academic_division_id' => 4, 'name' => 'Biología'],
            ['id' => 22, 'academic_division_id' => 4, 'name' => 'Gastronomía'],
            ['id' => 23, 'academic_division_id' => 4, 'name' => 'Gestión de Servicios Turísticos'],
            ['id' => 24, 'academic_division_id' => 4, 'name' => 'Lengua Inglesa'],
            ['id' => 25, 'academic_division_id' => 4, 'name' => 'Mercadotecnia y Negocios'],
            ['id' => 26, 'academic_division_id' => 8, 'name' => 'Administración Hotelera'],
            ['id' => 27, 'academic_division_id' => 8, 'name' => 'Derecho'],
            ['id' => 28, 'academic_division_id' => 8, 'name' => 'Gobierno y Gestión Pública'],
            ['id' => 29, 'academic_division_id' => 8, 'name' => 'Ingeniería Empresarial'],
            ['id' => 30, 'academic_division_id' => 8, 'name' => 'Agronegocios (mixta)'],
            ['id' => 31, 'academic_division_id' => 9, 'name' => 'Administración de Negocios del Entretenimiento y la Comunicación'],
            ['id' => 32, 'academic_division_id' => 9, 'name' => 'Administración Hotelera'],
            ['id' => 33, 'academic_division_id' => 9, 'name' => 'Derecho'],
            ['id' => 34, 'academic_division_id' => 9, 'name' => 'Enfermería'],
            ['id' => 35, 'academic_division_id' => 9, 'name' => 'Ingeniería en Redes y Ciberseguridad'],
            ['id' => 36, 'academic_division_id' => 9, 'name' => 'Lengua Inglesa'],
            ['id' => 37, 'academic_division_id' => 9, 'name' => 'Mercadotecnia y Negocios'],
            ['id' => 38, 'academic_division_id' => 9, 'name' => 'Psicología (mixta)'],
        ]);

        // 6. Edificios Reales
        Building::insert([
            ['id' => 1, 'campus_id' => 1, 'name' => 'Edificio de Rectoría'],
            ['id' => 2, 'campus_id' => 1, 'name' => 'Edificio de Cómputo y Sistemas'],
            ['id' => 3, 'campus_id' => 1, 'name' => 'Biblioteca Central'],
            ['id' => 4, 'campus_id' => 2, 'name' => 'Edificio de Aulas DCS'],
            ['id' => 5, 'campus_id' => 2, 'name' => 'Clínica Universitaria'],
        ]);

        // 7. Ubicaciones Físicas Reales
        Location::insert([
            ['id' => 1, 'building_id' => 2, 'name' => 'Site Principal (Site de Redes)', 'type' => 'Site'],
            ['id' => 2, 'building_id' => 2, 'name' => 'Laboratorio de Software', 'type' => 'Laboratorio'],
            ['id' => 3, 'building_id' => 1, 'name' => 'Oficina DGTI', 'type' => 'Oficina'],
            ['id' => 4, 'building_id' => 3, 'name' => 'Sala de Lectura', 'type' => 'Área Común'],
            ['id' => 5, 'building_id' => 4, 'name' => 'Baños Planta Baja', 'type' => 'Baño'],
        ]);

        // 8. Subdepartamentos / Oficinas Reales (El nuevo nivel)
        Subdepartment::insert([
            // Pertenecientes a la Dir. Gral. de Servicios Estudiantiles (ID 3)
            ['id' => 1, 'department_id' => 3, 'name' => 'Oficina de la DGSE'],
            ['id' => 2, 'department_id' => 3, 'name' => 'Departamento de Servicios Educativos Generales'],
            ['id' => 3, 'department_id' => 3, 'name' => 'Departamento de Apoyo a la Experiencia Educativa'],
            ['id' => 4, 'department_id' => 3, 'name' => 'Departamento de Desarrollo Universitario Integral'],

            // Pertenecientes a Rectoría (ID 1)
            ['id' => 5, 'department_id' => 1, 'name' => 'Oficina de Rectoría'],
            ['id' => 6, 'department_id' => 1, 'name' => 'Coordinación de Transparencia'],

            // Pertenecientes a la Dirección General de TI (ID 7)
            ['id' => 7, 'department_id' => 7, 'name' => 'Departamento de Redes y Telecomunicaciones'],
            ['id' => 8, 'department_id' => 7, 'name' => 'Departamento de Desarrollo de Sistemas'],
        ]);
    }
}
