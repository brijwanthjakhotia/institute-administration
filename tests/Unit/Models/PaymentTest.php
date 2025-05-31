<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Fee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private Payment $payment;
    private Student $student;
    private Fee $fee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = Student::factory()->create();
        $this->fee = Fee::factory()->create();
        $this->payment = Payment::factory()->create([
            'student_id' => $this->student->id,
            'fee_id' => $this->fee->id
        ]);
    }

    /** @test */
    public function it_has_required_attributes()
    {
        $this->assertNotNull($this->payment->student_id);
        $this->assertNotNull($this->payment->fee_id);
        $this->assertNotNull($this->payment->amount);
        $this->assertNotNull($this->payment->payment_date);
        $this->assertNotNull($this->payment->payment_method);
    }

    /** @test */
    public function it_belongs_to_a_student()
    {
        $this->assertInstanceOf(Student::class, $this->payment->student);
        $this->assertEquals($this->student->id, $this->payment->student->id);
    }

    /** @test */
    public function it_belongs_to_a_fee()
    {
        $this->assertInstanceOf(Fee::class, $this->payment->fee);
        $this->assertEquals($this->fee->id, $this->payment->fee->id);
    }

    /** @test */
    public function it_can_format_payment_method()
    {
        $this->payment->update(['payment_method' => 'bank_transfer']);
        $this->assertEquals('Bank Transfer', $this->payment->formattedPaymentMethod());

        $this->payment->update(['payment_method' => 'credit_card']);
        $this->assertEquals('Credit Card', $this->payment->formattedPaymentMethod());
    }

    /** @test */
    public function it_can_format_amount()
    {
        $this->payment->update(['amount' => 1000.50]);
        $this->assertEquals('$1,000.50', $this->payment->formattedAmount());
    }

    /** @test */
    public function it_can_format_payment_date()
    {
        $date = now();
        $this->payment->update(['payment_date' => $date]);
        $this->assertEquals($date->format('M d, Y'), $this->payment->formattedPaymentDate());
    }

    /** @test */
    public function it_can_generate_receipt_number()
    {
        $this->assertNotNull($this->payment->receipt_number);
        $this->assertStringStartsWith('REC-', $this->payment->receipt_number);
    }

    /** @test */
    public function it_updates_fee_status_when_created()
    {
        $fee = Fee::factory()->create(['amount' => 1000]);
        
        Payment::factory()->create([
            'fee_id' => $fee->id,
            'amount' => 1000
        ]);

        $this->assertEquals('paid', $fee->fresh()->status);
    }

    /** @test */
    public function it_updates_fee_status_when_deleted()
    {
        $fee = Fee::factory()->create(['amount' => 1000]);
        
        $payment = Payment::factory()->create([
            'fee_id' => $fee->id,
            'amount' => 1000
        ]);

        $this->assertEquals('paid', $fee->fresh()->status);

        $payment->delete();

        $this->assertEquals('pending', $fee->fresh()->status);
    }
} 