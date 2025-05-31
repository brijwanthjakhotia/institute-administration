<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Fee;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class FeeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private School $school;
    private ClassRoom $classRoom;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->school = School::factory()->create();
        $this->classRoom = ClassRoom::factory()->create(['school_id' => $this->school->id]);
    }

    /** @test */
    public function it_can_list_fees()
    {
        Fee::factory()->count(3)->create([
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/fees');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'amount',
                        'type',
                        'frequency',
                        'status',
                        'school',
                        'class_room'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_fee()
    {
        $feeData = [
            'name' => 'Tuition Fee',
            'description' => 'Monthly tuition fee',
            'amount' => 1000,
            'type' => 'tuition',
            'frequency' => 'monthly',
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id,
            'is_mandatory' => true
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/fees', $feeData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => $feeData['name'],
                    'amount' => $feeData['amount'],
                    'type' => $feeData['type']
                ]
            ]);

        $this->assertDatabaseHas('fees', [
            'name' => $feeData['name'],
            'amount' => $feeData['amount']
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_fee()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/fees', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'amount', 'type', 'frequency', 'school_id']);
    }

    /** @test */
    public function it_can_update_a_fee()
    {
        $fee = Fee::factory()->create([
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id
        ]);

        $updateData = [
            'name' => 'Updated Fee',
            'amount' => 1500,
            'type' => 'transportation',
            'frequency' => 'quarterly'
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/fees/{$fee->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => $updateData['name'],
                    'amount' => $updateData['amount'],
                    'type' => $updateData['type']
                ]
            ]);

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'name' => $updateData['name'],
            'amount' => $updateData['amount']
        ]);
    }

    /** @test */
    public function it_can_delete_a_fee()
    {
        $fee = Fee::factory()->create([
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/fees/{$fee->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('fees', [
            'id' => $fee->id
        ]);
    }

    /** @test */
    public function it_can_toggle_fee_status()
    {
        $fee = Fee::factory()->create([
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id,
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/fees/{$fee->id}/toggle-status");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'inactive'
                ]
            ]);

        $this->assertEquals('inactive', $fee->fresh()->status);
    }

    /** @test */
    public function it_can_show_fee_details()
    {
        $fee = Fee::factory()->create([
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/fees/{$fee->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $fee->id,
                    'name' => $fee->name,
                    'amount' => $fee->amount,
                    'type' => $fee->type,
                    'frequency' => $fee->frequency,
                    'school' => [
                        'id' => $this->school->id
                    ],
                    'class_room' => [
                        'id' => $this->classRoom->id
                    ]
                ]
            ]);
    }
} 