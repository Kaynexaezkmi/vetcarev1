<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $medicalRecords = MedicalRecord::with(['pet', 'appointment'])
            ->whereHas('pet', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('record_date', 'desc')
            ->paginate(15);

        return view('medical-records.index', compact('medicalRecords'));
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
