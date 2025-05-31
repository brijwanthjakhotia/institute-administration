<?php

namespace App\Http\Controllers;

use App\Models\ParentGuardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParentGuardianController extends Controller
{
    public function index()
    {
        $parents = ParentGuardian::with('students')->get();
        return view('parents.index', compact('parents'));
    }

    public function create()
    {
        return view('parents.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:parent_guardians',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'occupation' => 'required|string|max:255',
            'relationship' => 'required|in:father,mother,guardian',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ParentGuardian::create($request->all());

        return redirect()->route('parents.index')
            ->with('success', 'Parent/Guardian created successfully.');
    }

    public function show(ParentGuardian $parent)
    {
        $parent->load('students');
        return view('parents.show', compact('parent'));
    }

    public function edit(ParentGuardian $parent)
    {
        return view('parents.edit', compact('parent'));
    }

    public function update(Request $request, ParentGuardian $parent)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:parent_guardians,email,' . $parent->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'occupation' => 'required|string|max:255',
            'relationship' => 'required|in:father,mother,guardian',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $parent->update($request->all());

        return redirect()->route('parents.index')
            ->with('success', 'Parent/Guardian updated successfully.');
    }

    public function destroy(ParentGuardian $parent)
    {
        $parent->delete();
        return redirect()->route('parents.index')
            ->with('success', 'Parent/Guardian deleted successfully.');
    }
} 