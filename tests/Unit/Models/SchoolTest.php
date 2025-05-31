<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Fee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->school = School::factory()->create();
    }

    /** @test */
    public function it_has_required_attributes()
    {
        $this->assertNotNull($this->school->name);
        $this->assertNotNull($this->school->address);
        $this->assertNotNull($this->school->phone);
        $this->assertNotNull($this->school->email);
    }

    /** @test */
    public function it_has_many_class_rooms()
    {
        $classRooms = ClassRoom::factory()->count(3)->create([
            'school_id' => $this->school->id
        ]);

        $this->assertCount(3, $this->school->classRooms);
        $this->assertInstanceOf(ClassRoom::class, $this->school->classRooms->first());
    }

    /** @test */
    public function it_has_many_teachers()
    {
        $teachers = Teacher::factory()->count(3)->create([
            'school_id' => $this->school->id
        ]);

        $this->assertCount(3, $this->school->teachers);
        $this->assertInstanceOf(Teacher::class, $this->school->teachers->first());
    }

    /** @test */
    public function it_has_many_students()
    {
        $students = Student::factory()->count(3)->create([
            'school_id' => $this->school->id
        ]);

        $this->assertCount(3, $this->school->students);
        $this->assertInstanceOf(Student::class, $this->school->students->first());
    }

    /** @test */
    public function it_has_many_fees()
    {
        $fees = Fee::factory()->count(3)->create([
            'school_id' => $this->school->id
        ]);

        $this->assertCount(3, $this->school->fees);
        $this->assertInstanceOf(Fee::class, $this->school->fees->first());
    }

    /** @test */
    public function it_can_be_activated_and_deactivated()
    {
        $this->school->update(['status' => 'inactive']);
        $this->assertEquals('inactive', $this->school->fresh()->status);

        $this->school->update(['status' => 'active']);
        $this->assertEquals('active', $this->school->fresh()->status);
    }
} 