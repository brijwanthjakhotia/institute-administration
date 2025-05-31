<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::all();
        return view('schools.index', compact('schools'));
    }

    public function create()
    {
        return view('schools.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:schools',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'principal_name' => 'required|string|max:255',
            'established_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('schools/logos', 'public');
            $data['logo'] = $logoPath;
        }

        School::create($data);

        return redirect()->route('schools.index')
            ->with('success', 'School created successfully.');
    }

    public function show(School $school)
    {
        return view('schools.show', compact('school'));
    }

    public function edit(School $school)
    {
        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:schools,email,' . $school->id,
            'website' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'principal_name' => 'required|string|max:255',
            'established_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('schools/logos', 'public');
            $data['logo'] = $logoPath;
        }

        $school->update($data);

        return redirect()->route('schools.index')
            ->with('success', 'School updated successfully.');
    }

    public function destroy(School $school)
    {
        $school->delete();
        return redirect()->route('schools.index')
            ->with('success', 'School deleted successfully.');
    }
} 