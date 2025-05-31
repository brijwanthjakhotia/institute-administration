<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\ParentGuardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['school', 'classRoom', 'parentGuardian'])->get();
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $schools = School::all();
        $classRooms = ClassRoom::all();
        $parentGuardians = ParentGuardian::all();
        return view('students.create', compact('schools', 'classRooms', 'parentGuardians'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'school_id' => 'required|exists:schools,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'admission_number' => 'required|string|unique:students',
            'parent_guardian_id' => 'required|exists:parent_guardians,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Student::create($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['school', 'classRoom', 'parentGuardian', 'attendances', 'grades']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $schools = School::all();
        $classRooms = ClassRoom::all();
        $parentGuardians = ParentGuardian::all();
        return view('students.edit', compact('student', 'schools', 'classRooms', 'parentGuardians'));
    }

    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'school_id' => 'required|exists:schools,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'admission_number' => 'required|string|unique:students,admission_number,' . $student->id,
            'parent_guardian_id' => 'required|exists:parent_guardians,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }
} 