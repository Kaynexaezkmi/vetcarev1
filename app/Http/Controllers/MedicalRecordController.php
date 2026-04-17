<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pets = $user->pets()->orderBy('name')->get();
        $selectedPetId = $request->integer('pet_id');
        
        $medicalRecords = MedicalRecord::with(['pet', 'appointment'])
            ->whereHas('pet', function($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        if ($selectedPetId) {
            $medicalRecords->whereHas('pet', function ($query) use ($user, $selectedPetId) {
                $query->where('user_id', $user->id)
                    ->where('id', $selectedPetId);
            });
        }

        $medicalRecords = $medicalRecords
            ->orderBy('record_date', 'desc')
            ->paginate(15);

        return view('medical-records.index', compact('medicalRecords', 'pets', 'selectedPetId'));
    }

    public function markAsSeen(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        $medicalRecord->loadMissing('pet');

        if (!$medicalRecord->pet || $medicalRecord->pet->user_id !== $user->id) {
            abort(403);
        }

        if (!$medicalRecord->seen_by_user_at) {
            $medicalRecord->forceFill([
                'seen_by_user_at' => now(),
            ])->save();
        }

        $remainingUnread = MedicalRecord::whereHas('pet', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereNull('seen_by_user_at')->count();

        return response()->json([
            'success' => true,
            'remaining_unread' => $remainingUnread,
        ]);
    }
}
