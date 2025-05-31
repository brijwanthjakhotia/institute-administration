<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Student;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private School $school;
    private ClassRoom $classRoom;
    private Student $student;
    private Fee $fee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->school = School::factory()->create();
        $this->classRoom = ClassRoom::factory()->create(['school_id' => $this->school->id]);
        $this->student = Student::factory()->create([
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id
        ]);
        $this->fee = Fee::factory()->create([
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_can_list_payments()
    {
        Payment::factory()->count(3)->create([
            'student_id' => $this->student->id,
            'fee_id' => $this->fee->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/payments');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'amount',
                        'payment_date',
                        'payment_method',
                        'status',
                        'student',
                        'fee'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_payment()
    {
        $paymentData = [
            'student_id' => $this->student->id,
            'fee_id' => $this->fee->id,
            'amount' => 1000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'transaction_id' => 'TRX123',
            'notes' => 'Monthly payment'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/payments', $paymentData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'amount' => $paymentData['amount'],
                    'payment_method' => $paymentData['payment_method'],
                    'status' => 'completed'
                ]
            ]);

        $this->assertDatabaseHas('payments', [
            'student_id' => $this->student->id,
            'fee_id' => $this->fee->id,
            'amount' => $paymentData['amount']
        ]);

        $this->assertEquals('paid', $this->fee->fresh()->status);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_payment()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/payments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id', 'fee_id', 'amount', 'payment_date', 'payment_method']);
    }

    /** @test */
    public function it_can_update_a_payment()
    {
        $payment = Payment::factory()->create([
            'student_id' => $this->student->id,
            'fee_id' => $this->fee->id
        ]);

        $updateData = [
            'amount' => 1500,
            'payment_method' => 'bank_transfer',
            'transaction_id' => 'TRX456',
            'notes' => 'Updated payment'
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/payments/{$payment->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'amount' => $updateData['amount'],
                    'payment_method' => $updateData['payment_method']
                ]
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => $updateData['amount'],
            'payment_method' => $updateData['payment_method']
        ]);
    }

    /** @test */
    public function it_can_delete_a_payment()
    {
        $payment = Payment::factory()->create([
            'student_id' => $this->student->id,
            'fee_id' => $this->fee->id
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/payments/{$payment->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id
        ]);

        $this->assertEquals('pending', $this->fee->fresh()->status);
    }

    /** @test */
    public function it_can_generate_payment_receipt()
    {
        $payment = Payment::factory()->create([
            'student_id' => $this->student->id,
            'fee_id' => $this->fee->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/payments/{$payment->id}/receipt");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $payment->id,
                    'receipt_number' => $payment->receipt_number,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'student' => [
                        'id' => $this->student->id
                    ],
                    'fee' => [
                        'id' => $this->fee->id
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_show_payment_details()
    {
        $payment = Payment::factory()->create([
            'student_id' => $this->student->id,
            'fee_id' => $this->fee->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'student' => [
                        'id' => $this->student->id
                    ],
                    'fee' => [
                        'id' => $this->fee->id
                    ]
                ]
            ]);
    }
} 