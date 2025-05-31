<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\School;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeeController extends Controller
{
    public function index()
    {
        $fees = Fee::with(['school', 'classRoom'])->get();
        return view('fees.index', compact('fees'));
    }

    public function create()
    {
        $schools = School::all();
        $classRooms = ClassRoom::all();
        return view('fees.create', compact('schools', 'classRooms'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:tuition,transportation,library,laboratory,sports,other',
            'frequency' => 'required|in:one_time,monthly,quarterly,yearly',
            'school_id' => 'required|exists:schools,id',
            'class_room_id' => 'nullable|exists:class_rooms,id',
            'is_mandatory' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Fee::create($request->all());

        return redirect()->route('fees.index')
            ->with('success', 'Fee created successfully.');
    }

    public function show(Fee $fee)
    {
        $fee->load(['school', 'classRoom', 'payments']);
        return view('fees.show', compact('fee'));
    }

    public function edit(Fee $fee)
    {
        $schools = School::all();
        $classRooms = ClassRoom::all();
        return view('fees.edit', compact('fee', 'schools', 'classRooms'));
    }

    public function update(Request $request, Fee $fee)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:tuition,transportation,library,laboratory,sports,other',
            'frequency' => 'required|in:one_time,monthly,quarterly,yearly',
            'school_id' => 'required|exists:schools,id',
            'class_room_id' => 'nullable|exists:class_rooms,id',
            'is_mandatory' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $fee->update($request->all());

        return redirect()->route('fees.index')
            ->with('success', 'Fee updated successfully.');
    }

    public function destroy(Fee $fee)
    {
        $fee->delete();
        return redirect()->route('fees.index')
            ->with('success', 'Fee deleted successfully.');
    }

    public function toggleStatus(Fee $fee)
    {
        $fee->update([
            'status' => $fee->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->route('fees.index')
            ->with('success', 'Fee status updated successfully.');
    }
} 