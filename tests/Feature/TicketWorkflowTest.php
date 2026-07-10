<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Enums\StatusEnum;
use App\Models\Campus;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketGenderDetail;
use App\Models\TicketInfraDetail;
use App\Models\TicketSgcDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear Roles Spatie
        Role::firstOrCreate(['name' => RolesEnum::SUPER_ADMIN->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => RolesEnum::ADMIN->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => RolesEnum::RESPONSABLE_SGC->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => RolesEnum::RESPONSABLE_GENERO->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value, 'guard_name' => 'web']);

        // Crear Estatus Reales
        Status::firstOrCreate(['id' => 1, 'name' => 'Nuevo', 'color' => 'danger', 'is_closed' => 0]);
        Status::firstOrCreate(['id' => 2, 'name' => 'Asignado', 'color' => 'warning', 'is_closed' => 0]);
        Status::firstOrCreate(['id' => 3, 'name' => 'En Proceso', 'color' => 'info', 'is_closed' => 0]);
        Status::firstOrCreate(['id' => 4, 'name' => 'Resuelto', 'color' => 'success', 'is_closed' => 1]);
        Status::firstOrCreate(['id' => 5, 'name' => 'Cancelado', 'color' => 'gray', 'is_closed' => 1]);
    }

    /** @test */
    public function it_generates_folios_automatically_by_group()
    {
        $reporter = User::factory()->create();

        // 1. SGC Ticket
        $sgcTicket = Ticket::create([
            'reporter_id' => $reporter->id,
            'ticket_group' => 'SGC',
            'status_id' => StatusEnum::NUEVO->id(),
        ]);
        $this->assertStringStartsWith('SGC-' . now()->year . '-', $sgcTicket->folio);

        // 2. GENERO Ticket
        $generoTicket = Ticket::create([
            'reporter_id' => $reporter->id,
            'ticket_group' => 'GENERO',
            'status_id' => StatusEnum::NUEVO->id(),
        ]);
        $this->assertStringStartsWith('GEN-' . now()->year . '-', $generoTicket->folio);

        // 3. INFRAESTRUCTURA Ticket
        $infraTicket = Ticket::create([
            'reporter_id' => $reporter->id,
            'ticket_group' => 'INFRAESTRUCTURA',
            'status_id' => StatusEnum::NUEVO->id(),
        ]);
        $this->assertStringStartsWith('INFRA-' . now()->year . '-', $infraTicket->folio);
    }

    /** @test */
    public function it_records_history_on_creation_and_updates()
    {
        $reporter = User::factory()->create();
        $this->actingAs($reporter);

        // 1. Historial de Creación
        $ticket = Ticket::create([
            'reporter_id' => $reporter->id,
            'ticket_group' => 'SGC',
            'status_id' => StatusEnum::NUEVO->id(),
        ]);

        $this->assertDatabaseHas('ticket_histories', [
            'ticket_id' => $ticket->id,
            'action' => 'Creado',
        ]);

        // 2. Historial de Asignación
        $responsable = User::factory()->create();
        $ticket->update([
            'assigned_to_id' => $responsable->id,
            'status_id' => StatusEnum::ASIGNADO->id(),
        ]);

        $this->assertDatabaseHas('ticket_histories', [
            'ticket_id' => $ticket->id,
            'action' => 'Asignación',
            'new_value' => $responsable->name,
        ]);

        $this->assertDatabaseHas('ticket_histories', [
            'ticket_id' => $ticket->id,
            'action' => 'Cambio de Estado',
            'old_value' => 'Nuevo',
            'new_value' => 'Asignado',
        ]);
    }

    /** @test */
    public function it_restricts_cross_access_strictly_especially_for_genero()
    {
        $campusA = Campus::factory()->create(['name' => 'Campus Chetumal Bahía']);
        $campusB = Campus::factory()->create(['name' => 'Campus Playa del Carmen']);

        // Usuarios
        $userReporter = User::factory()->create();
        $userSgc = User::factory()->create();
        $userSgc->assignRole(RolesEnum::RESPONSABLE_SGC->value);
        $userSgc->campuses()->attach($campusA);

        $userInfra = User::factory()->create();
        $userInfra->assignRole(RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value);
        $userInfra->campuses()->attach($campusA);

        $userGeneroA = User::factory()->create();
        $userGeneroA->assignRole(RolesEnum::RESPONSABLE_GENERO->value);
        $userGeneroA->campuses()->attach($campusA);

        $userGeneroB = User::factory()->create();
        $userGeneroB->assignRole(RolesEnum::RESPONSABLE_GENERO->value);
        $userGeneroB->campuses()->attach($campusB);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RolesEnum::SUPER_ADMIN->value);

        // Crear ticket de Género en Campus A
        $ticketGenero = Ticket::create([
            'reporter_id' => $userReporter->id,
            'ticket_group' => 'GENERO',
            'status_id' => StatusEnum::NUEVO->id(),
        ]);
        TicketGenderDetail::create([
            'ticket_id' => $ticketGenero->id,
            'campus_id' => $campusA->id,
            'manifestation_type' => 'Acoso',
            'reported_person_name' => 'Persona X',
            'reported_person_type' => 'Externo',
            'chronological_narrative' => 'Narración',
        ]);

        // Verificaciones de Acceso (Policy view)
        
        // 1. Reportador del ticket: SÍ tiene acceso
        $this->assertTrue(Gate::forUser($userReporter)->allows('view', $ticketGenero));

        // 2. Super Admin: SÍ tiene acceso
        $this->assertTrue(Gate::forUser($superAdmin)->allows('view', $ticketGenero));

        // 3. Responsable de Género de Campus A (mismo campus): SÍ tiene acceso
        $this->assertTrue(Gate::forUser($userGeneroA)->allows('view', $ticketGenero));

        // 4. Responsable de Género de Campus B (diferente campus): NO tiene acceso (Aislado)
        $this->assertFalse(Gate::forUser($userGeneroB)->allows('view', $ticketGenero));

        // 5. Responsable de SGC (mismo campus pero diferente área): NO tiene acceso (Aislado)
        $this->assertFalse(Gate::forUser($userSgc)->allows('view', $ticketGenero));

        // 6. Responsable de Infraestructura (mismo campus pero diferente área): NO tiene acceso (Aislado)
        $this->assertFalse(Gate::forUser($userInfra)->allows('view', $ticketGenero));
    }
}
