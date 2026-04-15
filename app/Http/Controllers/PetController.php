<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Dog,Cat,Bird,Rabbit,Hamster,Fish,Reptile,Other',
            'breed' => 'nullable|string|max:255',
            'gender' => 'nullable|in:Male,Female',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Pet::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'breed' => $request->breed,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Pet added successfully!');
    }

    public function records(Pet $pet)
    {
        if ($pet->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $records = $pet->medicalRecords()->orderBy('record_date', 'desc')->get();

        return view('pets.records', compact('pet', 'records'));
    }
}
