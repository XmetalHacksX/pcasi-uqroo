<?php

namespace Tests\Feature;

use App\Models\DynamicForm;
use App\Models\DynamicFormStep;
use App\Models\DynamicFormField;
use App\Models\Ticket;
use App\Models\TicketSgcDetail;
use App\Models\User;
use App\Enums\StatusEnum;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear Estatus Reales
        Status::firstOrCreate(['id' => 1, 'name' => 'Nuevo', 'color' => 'danger', 'is_closed' => 0]);
    }

    /** @test */
    public function it_saves_and_unpacks_dynamic_attributes_on_details_models()
    {
        $reporter = User::factory()->create();

        // 1. Create a dynamic form field that is NOT a real database column
        $form = DynamicForm::create([
            'name' => 'SGC',
            'title' => 'SGC Form',
        ]);

        $step = DynamicFormStep::create([
            'dynamic_form_id' => $form->id,
            'title' => 'Step 1',
        ]);

        $field = DynamicFormField::create([
            'dynamic_form_step_id' => $step->id,
            'name' => 'telefono_contacto', // Custom field not in schema
            'label' => 'Teléfono de Contacto',
            'type' => 'text',
        ]);

        // 2. Create ticket and detail with the dynamic attribute
        $ticket = Ticket::create([
            'reporter_id' => $reporter->id,
            'ticket_group' => 'SGC',
            'status_id' => StatusEnum::NUEVO->id(),
        ]);

        $detail = TicketSgcDetail::create([
            'ticket_id' => $ticket->id,
            'classification' => 'Queja',
            'description' => 'Descripción de prueba',
            'telefono_contacto' => '9831234567', // Dynamic attribute
        ]);

        // 3. Assert database has the JSON structure in extra_attributes
        $this->assertDatabaseHas('ticket_sgc_details', [
            'id' => $detail->id,
            'classification' => 'Queja',
        ]);

        // Verify that the record actually serialized telefono_contacto to extra_attributes
        $dbDetail = TicketSgcDetail::find($detail->id);
        $this->assertNotNull($dbDetail->extra_attributes);
        $this->assertArrayHasKey('telefono_contacto', $dbDetail->extra_attributes);
        $this->assertEquals('9831234567', $dbDetail->extra_attributes['telefono_contacto']);

        // 4. Assert that the attribute is unpacked automatically on retrieval
        $this->assertEquals('9831234567', $dbDetail->telefono_contacto);
    }
}
