<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with(['student', 'subject', 'classRoom'])->get();
        return view('grades.index', compact('grades'));
    }

    public function create()
    {
        $students = Student::all();
        $subjects = Subject::all();
        $classRooms = ClassRoom::all();
        return view('grades.create', compact('students', 'subjects', 'classRooms'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'exam_type' => 'required|string|max:50',
            'marks_obtained' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0',
            'grade_letter' => 'required|string|max:2',
            'remarks' => 'nullable|string',
            'exam_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Grade::create($request->all());

        return redirect()->route('grades.index')
            ->with('success', 'Grade recorded successfully.');
    }

    public function show(Grade $grade)
    {
        $grade->load(['student', 'subject', 'classRoom']);
        return view('grades.show', compact('grade'));
    }

    public function edit(Grade $grade)
    {
        $students = Student::all();
        $subjects = Subject::all();
        $classRooms = ClassRoom::all();
        return view('grades.edit', compact('grade', 'students', 'subjects', 'classRooms'));
    }

    public function update(Request $request, Grade $grade)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_room_id' => 'required|exists:class_rooms,id',
            'exam_type' => 'required|string|max:50',
            'marks_obtained' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0',
            'grade_letter' => 'required|string|max:2',
            'remarks' => 'nullable|string',
            'exam_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $grade->update($request->all());

        return redirect()->route('grades.index')
            ->with('success', 'Grade updated successfully.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('grades.index')
            ->with('success', 'Grade deleted successfully.');
    }

    public function bulkCreate()
    {
        $classRooms = ClassRoom::all();
        $subjects = Subject::all();
        return view('grades.bulk-create', compact('classRooms', 'subjects'));
    }

    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_room_id' => 'required|exists:class_rooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_type' => 'required|string|max:50',
            'exam_date' => 'required|date',
            'total_marks' => 'required|numeric|min:0',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.marks_obtained' => 'required|numeric|min:0',
            'grades.*.grade_letter' => 'required|string|max:2',
            'grades.*.remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($request->grades as $grade) {
            Grade::create([
                'student_id' => $grade['student_id'],
                'subject_id' => $request->subject_id,
                'class_room_id' => $request->class_room_id,
                'exam_type' => $request->exam_type,
                'exam_date' => $request->exam_date,
                'marks_obtained' => $grade['marks_obtained'],
                'total_marks' => $request->total_marks,
                'grade_letter' => $grade['grade_letter'],
                'remarks' => $grade['remarks'] ?? null,
            ]);
        }

        return redirect()->route('grades.index')
            ->with('success', 'Bulk grades recorded successfully.');
    }
} 