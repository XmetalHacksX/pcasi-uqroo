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

    /** @test */
    public function it_can_perform_crud_operations_on_forms_steps_and_fields()
    {
        // 1. CREATE
        $form = DynamicForm::create([
            'name' => 'TEST_FORM',
            'title' => 'Formulario de Prueba',
            'description' => 'Descripción de prueba',
        ]);

        $step1 = DynamicFormStep::create([
            'dynamic_form_id' => $form->id,
            'title' => 'Sección 1',
            'description' => 'Paso 1 del formulario',
            'sort_order' => 1,
        ]);

        $field1 = DynamicFormField::create([
            'dynamic_form_step_id' => $step1->id,
            'name' => 'correo_alterno',
            'label' => 'Correo Alternativo',
            'type' => 'text',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $field2 = DynamicFormField::create([
            'dynamic_form_step_id' => $step1->id,
            'name' => 'satisfecho',
            'label' => '¿Está satisfecho?',
            'type' => 'toggle',
            'is_required' => false,
            'sort_order' => 2,
        ]);

        $this->assertDatabaseHas('dynamic_forms', ['name' => 'TEST_FORM']);
        $this->assertDatabaseHas('dynamic_form_steps', ['title' => 'Sección 1']);
        $this->assertDatabaseHas('dynamic_form_fields', ['name' => 'correo_alterno']);
        $this->assertDatabaseHas('dynamic_form_fields', ['name' => 'satisfecho']);

        // Verify relationships
        $this->assertCount(1, $form->steps);
        $this->assertCount(2, $step1->fields);

        // 2. UPDATE (Edit step & fields)
        $step1->update([
            'title' => 'Sección 1 Editada',
        ]);

        $field1->update([
            'label' => 'Nuevo Correo Alternativo',
            'is_required' => false,
        ]);

        $this->assertDatabaseHas('dynamic_form_steps', ['id' => $step1->id, 'title' => 'Sección 1 Editada']);
        $this->assertDatabaseHas('dynamic_form_fields', ['id' => $field1->id, 'label' => 'Nuevo Correo Alternativo', 'is_required' => false]);

        // 3. SORTING / REORDERING
        $step2 = DynamicFormStep::create([
            'dynamic_form_id' => $form->id,
            'title' => 'Sección 2',
            'sort_order' => 2,
        ]);

        // Reorder steps
        $step1->update(['sort_order' => 2]);
        $step2->update(['sort_order' => 1]);

        $orderedSteps = $form->steps()->get();
        $this->assertEquals($step2->id, $orderedSteps->first()->id);
        $this->assertEquals($step1->id, $orderedSteps->last()->id);

        // Reorder fields
        $field1->update(['sort_order' => 2]);
        $field2->update(['sort_order' => 1]);

        $orderedFields = $step1->fields()->get();
        $this->assertEquals($field2->id, $orderedFields->first()->id);
        $this->assertEquals($field1->id, $orderedFields->last()->id);

        // 4. DELETE
        $field2->delete();
        $this->assertDatabaseMissing('dynamic_form_fields', ['id' => $field2->id]);

        $step1->delete(); // This should cascade delete field1
        $this->assertDatabaseMissing('dynamic_form_steps', ['id' => $step1->id]);
        $this->assertDatabaseMissing('dynamic_form_fields', ['id' => $field1->id]);

        $form->delete(); // This should cascade delete step2
        $this->assertDatabaseMissing('dynamic_forms', ['id' => $form->id]);
        $this->assertDatabaseMissing('dynamic_form_steps', ['id' => $step2->id]);
    }
}
