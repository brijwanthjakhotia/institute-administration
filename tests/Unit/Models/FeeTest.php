<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Fee;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeeTest extends TestCase
{
    use RefreshDatabase;

    private Fee $fee;
    private School $school;
    private ClassRoom $classRoom;

    protected function setUp(): void
    {
        parent::setUp();
        $this->school = School::factory()->create();
        $this->classRoom = ClassRoom::factory()->create(['school_id' => $this->school->id]);
        $this->fee = Fee::factory()->create([
            'school_id' => $this->school->id,
            'class_room_id' => $this->classRoom->id
        ]);
    }

    /** @test */
    public function it_has_required_attributes()
    {
        $this->assertNotNull($this->fee->name);
        $this->assertNotNull($this->fee->amount);
        $this->assertNotNull($this->fee->type);
        $this->assertNotNull($this->fee->frequency);
        $this->assertNotNull($this->fee->school_id);
    }

    /** @test */
    public function it_belongs_to_a_school()
    {
        $this->assertInstanceOf(School::class, $this->fee->school);
        $this->assertEquals($this->school->id, $this->fee->school->id);
    }

    /** @test */
    public function it_belongs_to_a_class_room()
    {
        $this->assertInstanceOf(ClassRoom::class, $this->fee->classRoom);
        $this->assertEquals($this->classRoom->id, $this->fee->classRoom->id);
    }

    /** @test */
    public function it_has_many_payments()
    {
        $payments = Payment::factory()->count(3)->create([
            'fee_id' => $this->fee->id
        ]);

        $this->assertCount(3, $this->fee->payments);
        $this->assertInstanceOf(Payment::class, $this->fee->payments->first());
    }

    /** @test */
    public function it_can_calculate_total_paid_amount()
    {
        Payment::factory()->count(3)->create([
            'fee_id' => $this->fee->id,
            'amount' => 100
        ]);

        $this->assertEquals(300, $this->fee->totalPaid());
    }

    /** @test */
    public function it_can_check_if_fully_paid()
    {
        $this->fee->update(['amount' => 300]);

        Payment::factory()->create([
            'fee_id' => $this->fee->id,
            'amount' => 100
        ]);

        $this->assertFalse($this->fee->isFullyPaid());

        Payment::factory()->create([
            'fee_id' => $this->fee->id,
            'amount' => 200
        ]);

        $this->assertTrue($this->fee->isFullyPaid());
    }

    /** @test */
    public function it_can_be_toggled_active()
    {
        $this->fee->update(['status' => 'inactive']);
        $this->assertEquals('inactive', $this->fee->fresh()->status);

        $this->fee->update(['status' => 'active']);
        $this->assertEquals('active', $this->fee->fresh()->status);
    }
} 