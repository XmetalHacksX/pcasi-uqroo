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
        $statuses = [
            ['id' => 1, 'name' => 'Nuevo', 'color' => 'danger', 'is_closed' => 0],
            ['id' => 2, 'name' => 'Asignado', 'color' => 'warning', 'is_closed' => 0],
            ['id' => 3, 'name' => 'En Proceso', 'color' => 'info', 'is_closed' => 0],
            ['id' => 4, 'name' => 'Resuelto', 'color' => 'success', 'is_closed' => 1],
            ['id' => 5, 'name' => 'Cancelado', 'color' => 'gray', 'is_closed' => 1],
        ];
        foreach ($statuses as $status) {
            Status::updateOrCreate(['id' => $status['id']], $status);
        }

        // 2. Campus Reales
        $campuses = [
            ['id' => 1, 'name' => 'Campus Chetumal Bahía'],
            ['id' => 2, 'name' => 'Campus Chetumal Salud'],
            ['id' => 3, 'name' => 'Campus Playa del Carmen'],
            ['id' => 4, 'name' => 'Campus Cozumel'],
            ['id' => 5, 'name' => 'Campus Cancún'],
            ['id' => 6, 'name' => 'Campus Felipe Carrillo Puerto'],
        ];
        foreach ($campuses as $campus) {
            Campus::updateOrCreate(['id' => $campus['id']], $campus);
        }

        // 3. Departamentos (Direcciones Generales y Divisiones Académicas)
        $departments = [
            // Administrativos (Apoyos)
            ['id' => 1, 'campus_id' => 1, 'name' => 'Rectoría'],
            ['id' => 2, 'campus_id' => 1, 'name' => 'Secretaría General'],
            ['id' => 3, 'campus_id' => 1, 'name' => 'Dirección General de Servicios Estudiantiles'],
            ['id' => 4, 'campus_id' => 1, 'name' => 'Dirección General de Asuntos Jurídicos'],
            ['id' => 5, 'campus_id' => 1, 'name' => 'Dirección General de Desarrollo Académico'],
            ['id' => 6, 'campus_id' => 1, 'name' => 'Dirección General de Investigación, Posgrado y Vinculación'],
            ['id' => 7, 'campus_id' => 1, 'name' => 'Dirección General de Tecnologías de la Información'],
            ['id' => 8, 'campus_id' => 1, 'name' => 'Dirección General de Imagen Institucional y Comunicación'],
            ['id' => 9, 'campus_id' => 1, 'name' => 'Dirección General de Planeación'],
            ['id' => 10, 'campus_id' => 1, 'name' => 'Dirección General de Administración y Finanzas'],
            ['id' => 11, 'campus_id' => 1, 'name' => 'Auditoría Interna'],

            // Académicos (Divisiones) - Usados para asociar sus Departamentos Académicos
            ['id' => 12, 'campus_id' => 1, 'name' => 'División de Ciencias, Ingeniería y Tecnología'],
            ['id' => 13, 'campus_id' => 1, 'name' => 'División de Ciencias Políticas, Económicas y Administrativas'],
            ['id' => 14, 'campus_id' => 2, 'name' => 'División de Ciencias de la Salud'],
            ['id' => 15, 'campus_id' => 4, 'name' => 'División de Ciencias Multidisciplinarias Cozumel'],
            ['id' => 16, 'campus_id' => 3, 'name' => 'División de Ciencias Multidisciplinarias Playa del Carmen'],
            ['id' => 17, 'campus_id' => 5, 'name' => 'División de Ciencias Multidisciplinarias Cancún'],
            ['id' => 18, 'campus_id' => 1, 'name' => 'División de Ciencias Sociales y Humanidades'],
            ['id' => 19, 'campus_id' => 6, 'name' => 'División de Ciencias Sociales y Humanidades'],
            ['id' => 20, 'campus_id' => 6, 'name' => 'División de Ciencias, Ingeniería y Tecnología'],
        ];
        foreach ($departments as $department) {
            Department::updateOrCreate(['id' => $department['id']], $department);
        }

        // 4. Divisiones Académicas Reales (Respetando IDs originales)
        $divisions = [
            ['id' => 1, 'campus_id' => 1, 'name' => 'División de Ciencias, Ingeniería y Tecnología', 'acronym' => 'DCIT'],
            ['id' => 2, 'campus_id' => 1, 'name' => 'División de Ciencias Políticas, Económicas y Administrativas', 'acronym' => 'DCPEA'],
            ['id' => 3, 'campus_id' => 2, 'name' => 'División de Ciencias de la Salud', 'acronym' => 'DCS'],
            ['id' => 4, 'campus_id' => 4, 'name' => 'División de Ciencias Multidisciplinarias Cozumel', 'acronym' => 'DCMCOZ'],
            ['id' => 8, 'campus_id' => 3, 'name' => 'División de Ciencias Multidisciplinarias Playa del Carmen', 'acronym' => 'DCMPC'],
            ['id' => 9, 'campus_id' => 5, 'name' => 'División de Ciencias Multidisciplinarias Cancún', 'acronym' => 'DCMC'],
            ['id' => 10, 'campus_id' => 6, 'name' => 'División de Ciencias Sociales y Humanidades', 'acronym' => 'DCSH'],
            ['id' => 11, 'campus_id' => 6, 'name' => 'División de Ciencias, Ingeniería y Tecnología', 'acronym' => 'DCIT'],
            ['id' => 12, 'campus_id' => 1, 'name' => 'División de Ciencias Sociales y Humanidades', 'acronym' => 'DCSH'],
        ];
        foreach ($divisions as $division) {
            AcademicDivision::updateOrCreate(['id' => $division['id']], $division);
        }

        // 5. Programas Educativos Reales
        $programs = [
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
        ];
        foreach ($programs as $program) {
            EducationalProgram::updateOrCreate(['id' => $program['id']], $program);
        }

        // 6. Edificios Reales
        $buildings = [
            ['id' => 1, 'campus_id' => 1, 'name' => 'Edificio de Rectoría'],
            ['id' => 2, 'campus_id' => 1, 'name' => 'Edificio de Cómputo y Sistemas'],
            ['id' => 3, 'campus_id' => 1, 'name' => 'Biblioteca Central'],
            ['id' => 4, 'campus_id' => 2, 'name' => 'Edificio de Aulas DCS'],
            ['id' => 5, 'campus_id' => 2, 'name' => 'Clínica Universitaria'],
        ];
        foreach ($buildings as $building) {
            Building::updateOrCreate(['id' => $building['id']], $building);
        }

        // 7. Ubicaciones Físicas Reales
        $locations = [
            ['id' => 1, 'building_id' => 2, 'name' => 'Site Principal (Site de Redes)', 'type' => 'Site'],
            ['id' => 2, 'building_id' => 2, 'name' => 'Laboratorio de Software', 'type' => 'Laboratorio'],
            ['id' => 3, 'building_id' => 1, 'name' => 'Oficina DGTI', 'type' => 'Oficina'],
            ['id' => 4, 'building_id' => 3, 'name' => 'Sala de Lectura', 'type' => 'Área Común'],
            ['id' => 5, 'building_id' => 4, 'name' => 'Baños Planta Baja', 'type' => 'Baño'],
        ];
        foreach ($locations as $location) {
            Location::updateOrCreate(['id' => $location['id']], $location);
        }

        // 8. Subdepartamentos / Oficinas / Departamentos Académicos Reales
        $subdepartments = [
            // Rectoría (ID 1)
            ['id' => 1, 'department_id' => 1, 'name' => 'Unidad de Transparencia'],
            ['id' => 2, 'department_id' => 1, 'name' => 'Departamento de Control y Gestión'],
            ['id' => 3, 'department_id' => 1, 'name' => 'Unidad de Igualdad e Inclusión'],

            // Auditoría Interna (ID 11)
            ['id' => 4, 'department_id' => 11, 'name' => 'Departamento de Auditoría'],

            // Dirección General de Servicios Estudiantiles (ID 3)
            ['id' => 5, 'department_id' => 3, 'name' => 'Departamento de Servicios Educativos Generales'],
            ['id' => 6, 'department_id' => 3, 'name' => 'Departamento de Apoyo a la Experiencia Educativa'],
            ['id' => 7, 'department_id' => 3, 'name' => 'Departamento de Desarrollo Universitario Integral'],

            // Dirección General de Desarrollo Académico (ID 5)
            ['id' => 8, 'department_id' => 5, 'name' => 'Departamento de Fortalecimiento de la Docencia'],
            ['id' => 9, 'department_id' => 5, 'name' => 'Departamento de Innovación y Multimedia Educativa'],

            // Dirección General de Investigación, Posgrado y Vinculación (ID 6)
            ['id' => 10, 'department_id' => 6, 'name' => 'Departamento de Investigación y Posgrado'],
            ['id' => 11, 'department_id' => 6, 'name' => 'Departamento de Vinculación Universitaria'],

            // Dirección General de Tecnologías de la Información (ID 7)
            ['id' => 12, 'department_id' => 7, 'name' => 'Departamento de Infraestructura Tecnológica'],
            ['id' => 13, 'department_id' => 7, 'name' => 'Departamento de Desarrollo de Sistemas'],

            // Dirección General de Imagen Institucional y Comunicación (ID 8)
            ['id' => 14, 'department_id' => 8, 'name' => 'Departamento de Comunicación y Publicaciones'],
            ['id' => 15, 'department_id' => 8, 'name' => 'Departamento de Producción y Multimedia'],

            // Dirección General de Planeación (ID 9)
            ['id' => 16, 'department_id' => 9, 'name' => 'Departamento de Planeación y Programación'],
            ['id' => 17, 'department_id' => 9, 'name' => 'Departamento de Integración y Control Presupuestal'],
            ['id' => 18, 'department_id' => 9, 'name' => 'Departamento de Seguimiento y Evaluación'],
            ['id' => 19, 'department_id' => 9, 'name' => 'Departamento de Gestión de la Calidad'],

            // Dirección General de Administración y Finanzas (ID 10)
            ['id' => 20, 'department_id' => 10, 'name' => 'Departamento de Recursos Humanos'],
            ['id' => 21, 'department_id' => 10, 'name' => 'Departamento de Recursos Financieros'],
            ['id' => 22, 'department_id' => 10, 'name' => 'Departamento de Recursos Materiales'],
            ['id' => 23, 'department_id' => 10, 'name' => 'Departamento de Infraestructura'],

            // Dirección General de Asuntos Jurídicos (ID 4)
            ['id' => 24, 'department_id' => 4, 'name' => 'Departamento Contencioso'],

            // --- DEPARTAMENTOS ACADÉMICOS ASOCIADOS A DIVISIONES ---

            // División de Ciencias, Ingeniería y Tecnología (Chetumal Bahía - ID 12)
            ['id' => 25, 'department_id' => 12, 'name' => 'Consejo de División'],
            ['id' => 26, 'department_id' => 12, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 27, 'department_id' => 12, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 28, 'department_id' => 12, 'name' => 'Departamento de Ciencias Ambientales'],
            ['id' => 29, 'department_id' => 12, 'name' => 'Departamento de Ingeniería y Tecnología'],

            // División de Ciencias Políticas, Económicas y Administrativas (Chetumal Bahía - ID 13)
            ['id' => 30, 'department_id' => 13, 'name' => 'Consejo de División'],
            ['id' => 31, 'department_id' => 13, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 32, 'department_id' => 13, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 33, 'department_id' => 13, 'name' => 'Departamento de Ciencias Políticas'],
            ['id' => 34, 'department_id' => 13, 'name' => 'Departamento de Ciencias Económicas y Administrativas'],

            // División de Ciencias de la Salud (Chetumal Salud - ID 14)
            ['id' => 35, 'department_id' => 14, 'name' => 'Consejo de División'],
            ['id' => 36, 'department_id' => 14, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 37, 'department_id' => 14, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 38, 'department_id' => 14, 'name' => 'Departamento de Ciencias Médicas'],
            ['id' => 39, 'department_id' => 14, 'name' => 'Departamento de Ciencias de Enfermería'],
            ['id' => 40, 'department_id' => 14, 'name' => 'Departamento de Ciencias Farmacéuticas'],

            // División de Ciencias Multidisciplinarias Cozumel (Cozumel - ID 15)
            ['id' => 41, 'department_id' => 15, 'name' => 'Consejo de División'],
            ['id' => 42, 'department_id' => 15, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 43, 'department_id' => 15, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 44, 'department_id' => 15, 'name' => 'Departamento de Ciencias Empresariales y Sostenibilidad'],
            ['id' => 45, 'department_id' => 15, 'name' => 'Departamento de Humanidades y Ciencias'],

            // División de Ciencias Multidisciplinarias Playa del Carmen (Playa del Carmen - ID 16)
            ['id' => 46, 'department_id' => 16, 'name' => 'Consejo de División'],
            ['id' => 47, 'department_id' => 16, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 48, 'department_id' => 16, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 49, 'department_id' => 16, 'name' => 'Departamento de Ciencias Empresariales'],
            ['id' => 50, 'department_id' => 16, 'name' => 'Departamento de Ciencias Sociales'],

            // División de Ciencias Multidisciplinarias Cancún (Cancún - ID 17)
            ['id' => 51, 'department_id' => 17, 'name' => 'Consejo de División'],
            ['id' => 52, 'department_id' => 17, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 53, 'department_id' => 17, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 54, 'department_id' => 17, 'name' => 'Departamento de Ciencias Administrativas y Negocios'],
            ['id' => 55, 'department_id' => 17, 'name' => 'Departamento de Ciencias Sociales y Humanidades'],
            ['id' => 56, 'department_id' => 17, 'name' => 'Departamento de Ciencias de la Salud y Tecnología'],

            // División de Ciencias Sociales y Humanidades (Chetumal Bahía - ID 18)
            ['id' => 57, 'department_id' => 18, 'name' => 'Consejo de División'],
            ['id' => 58, 'department_id' => 18, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 59, 'department_id' => 18, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 60, 'department_id' => 18, 'name' => 'Departamento de Lengua y Educación'],
            ['id' => 61, 'department_id' => 18, 'name' => 'Departamento de Humanidades'],
            ['id' => 62, 'department_id' => 18, 'name' => 'Departamento de Ciencias Sociales'],

            // División de Ciencias Sociales y Humanidades (Felipe Carrillo Puerto - ID 19)
            ['id' => 63, 'department_id' => 19, 'name' => 'Consejo de División'],
            ['id' => 64, 'department_id' => 19, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 65, 'department_id' => 19, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 66, 'department_id' => 19, 'name' => 'Departamento de Lengua y Educación'],
            ['id' => 67, 'department_id' => 19, 'name' => 'Departamento de Humanidades'],
            ['id' => 68, 'department_id' => 19, 'name' => 'Departamento de Ciencias Sociales'],

            // División de Ciencias, Ingeniería y Tecnología (Felipe Carrillo Puerto - ID 20)
            ['id' => 69, 'department_id' => 20, 'name' => 'Consejo de División'],
            ['id' => 70, 'department_id' => 20, 'name' => 'Secretaría Técnica de Docencia'],
            ['id' => 71, 'department_id' => 20, 'name' => 'Secretaría Técnica de Investigación y Extensión'],
            ['id' => 72, 'department_id' => 20, 'name' => 'Departamento de Ciencias Ambientales'],
            ['id' => 73, 'department_id' => 20, 'name' => 'Departamento de Ingeniería y Tecnología'],
        ];
        foreach ($subdepartments as $subdepartment) {
            Subdepartment::updateOrCreate(['id' => $subdepartment['id']], $subdepartment);
        }
    }
}

