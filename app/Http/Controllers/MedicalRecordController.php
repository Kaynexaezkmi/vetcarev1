<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\PetActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $pets = $user->pets()->orderBy('name')->get();
        $selectedPetId = $request->integer('pet_id');
        $selectedPet = $selectedPetId
            ? $pets->firstWhere('id', $selectedPetId)
            : null;

        $medicalRecords = MedicalRecord::with(['pet', 'appointment'])
            ->whereHas('pet', function ($query) use ($user) {
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

        $activityLogs = PetActivityLog::with(['actor', 'pet'])
            ->whereIn('pet_id', $pets->pluck('id'))
            ->when($selectedPet, fn ($query) => $query->where('pet_id', $selectedPet->id))
            ->latest()
            ->limit(8)
            ->get();

        return view('medical-records.index', compact('medicalRecords', 'pets', 'selectedPetId', 'selectedPet', 'activityLogs'));
    }

    public function markAsSeen(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        $medicalRecord->loadMissing('pet');

        if (! $medicalRecord->pet || $medicalRecord->pet->user_id !== $user->id) {
            abort(403);
        }

        if (! $medicalRecord->seen_by_user_at) {
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
