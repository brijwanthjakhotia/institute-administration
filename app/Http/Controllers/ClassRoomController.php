<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassRoomController extends Controller
{
    public function index()
    {
        $classRooms = ClassRoom::with('school')->get();
        return view('classrooms.index', compact('classRooms'));
    }

    public function create()
    {
        $schools = School::all();
        return view('classrooms.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'grade_level' => 'required|string|max:50',
            'section' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1',
            'room_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ClassRoom::create($request->all());

        return redirect()->route('classrooms.index')
            ->with('success', 'Class room created successfully.');
    }

    public function show(ClassRoom $classRoom)
    {
        $classRoom->load(['school', 'students', 'teachers', 'subjects']);
        return view('classrooms.show', compact('classRoom'));
    }

    public function edit(ClassRoom $classRoom)
    {
        $schools = School::all();
        return view('classrooms.edit', compact('classRoom', 'schools'));
    }

    public function update(Request $request, ClassRoom $classRoom)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'grade_level' => 'required|string|max:50',
            'section' => 'required|string|max:10',
            'capacity' => 'required|integer|min:1',
            'room_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $classRoom->update($request->all());

        return redirect()->route('classrooms.index')
            ->with('success', 'Class room updated successfully.');
    }

    public function destroy(ClassRoom $classRoom)
    {
        $classRoom->delete();
        return redirect()->route('classrooms.index')
            ->with('success', 'Class room deleted successfully.');
    }
} 