<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\MedicalRecord;
use App\Models\Inquiry;
use App\Models\Reminder;
use App\Models\User;
use App\Models\Feedback;
use App\Mail\AppointmentReminder;
use App\Mail\AppointmentStatusNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function appointments(Request $request)
    {
        $query = Appointment::with(['pet', 'user', 'service'])
            ->whereIn('status', ['pending', 'approved', 'rejected', 'cancelled']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->where('appointment_date', $request->date);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('pet', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);

        $completedAppointments = Appointment::with(['pet', 'user', 'service'])
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('admin.appointments.index', compact('appointments', 'completedAppointments'));
    }

    public function showAppointment(Appointment $appointment)
    {
        return redirect()->route('admin.appointments.index');
    }

    public function approveAppointment(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $appointmentDate = Carbon::parse($appointment->appointment_date);
        $reminderDate = $appointmentDate->copy()->subDay()->setTime(9, 0);
        
        if ($reminderDate->isPast() || $reminderDate->isToday()) {
            $reminderDate = now();
        }
        
        Reminder::create([
            'user_id' => $appointment->user_id,
            'appointment_id' => $appointment->id,
            'type' => 'email',
            'send_at' => $reminderDate,
            'is_sent' => false,
        ]);

        $message = $reminderDate->isPast() || $reminderDate->isToday() 
            ? 'Appointment approved successfully! Reminder will be sent immediately.' 
            : 'Appointment approved successfully! Reminder will be sent 1 day before.';

        return redirect()->back()->with('success', $message);
    }

    public function cancelAppointment(Appointment $appointment)
    {
        return $this->rejectAppointment(request(), $appointment);
    }

    public function rejectAppointment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $appointment->update([
            'status' => 'rejected',
            'cancellation_reason' => $request->reason,
            'cancelled_by' => 'admin',
            'notes' => trim(($appointment->notes ? $appointment->notes . "\n" : '') . 'Rejection reason: ' . $request->reason),
        ]);

        $this->sendAppointmentStatusNotification($appointment->fresh(['pet', 'user', 'service']), 'Rejected');

        return redirect()->back()->with('success', 'Appointment rejected and email notification sent.');
    }

    public function completeAppointment(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Appointment marked as completed!');
    }

    public function patients(Request $request)
    {
        $query = User::where('role', 'user')
            ->whereHas('pets', function($petQuery) {
                $petQuery->whereHas('appointments', function($aptQuery) {
                    $aptQuery->whereIn('status', ['pending', 'approved', 'completed']);
                });
            })
            ->with(['pets' => function($petQuery) {
                $petQuery->whereHas('appointments', function($aptQuery) {
                    $aptQuery->whereIn('status', ['pending', 'approved', 'completed']);
                });
            }]);

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhereHas('pets', function($petQuery) use ($search) {
                      $petQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $owners = $query->orderBy('name')->paginate(15);

        return view('admin.patients.index', compact('owners'));
    }

    public function patientRecords(Pet $pet)
    {
        $user = $pet->user;
        $allPets = $user->pets()->orderBy('name')->get();
        
        $records = $pet->medicalRecords()->with('creator')->orderBy('record_date', 'desc')->get();
        $appointments = $pet->appointments()->orderBy('appointment_date', 'desc')->get();
        $recordSubmissionToken = (string) Str::uuid();
        
        return view('admin.patients.records', compact('pet', 'records', 'appointments', 'allPets', 'user', 'recordSubmissionToken'));
    }

    public function storeMedicalRecord(Request $request, Pet $pet)
    {
        $request->validate([
            'pet_name' => 'required|string|max:255',
            'pet_gender' => 'nullable|string|in:Male,Female',
            'pet_type' => 'required|string|in:Dog,Cat,Bird,Rabbit,Hamster,Fish,Reptile,Other',
            'pet_breed' => 'nullable|string|max:255',
            'pet_dob' => 'nullable|date',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string',
            'next_call' => 'nullable|string',
            'record_date' => 'required|date',
            'submission_token' => 'required|string|max:255',
        ]);

        if (MedicalRecord::where('submission_token', $request->submission_token)->exists()) {
            return redirect()->back()->with('success', 'Medical record already saved.');
        }

        $pet->update([
            'name' => $request->pet_name,
            'gender' => $request->pet_gender,
            'type' => $request->pet_type,
            'breed' => $request->pet_breed,
            'date_of_birth' => $request->pet_dob,
        ]);

        $data = [
            'pet_id' => $pet->id,
            'title' => 'Medical Record - ' . $request->record_date,
            'diagnosis' => $request->filled('diagnosis') ? trim($request->diagnosis) : null,
            'treatment' => $request->filled('treatment') ? trim($request->treatment) : null,
            'notes' => $request->notes,
            'next_call' => $request->filled('next_call') ? trim($request->next_call) : null,
            'record_date' => $request->record_date,
            'created_by' => Auth::id(),
            'submission_token' => $request->submission_token,
        ];

        MedicalRecord::create($data);

        return redirect()->back()->with('success', 'Medical record added successfully!');
    }

    public function deleteMedicalRecord(MedicalRecord $record)
    {
        if ($record->file_path) {
            Storage::disk('public')->delete($record->file_path);
        }
        $record->delete();

        return redirect()->back()->with('success', 'Medical record deleted.');
    }

    public function inquiries(Request $request)
    {
        $status = $request->get('status', '');
        
        $query = Inquiry::query();

        if ($status === 'new') {
            $query->where('status', 'new');
        } elseif ($status === 'replied') {
            $query->where('status', 'replied');
        } elseif ($status === 'closed') {
            $query->where('status', 'closed');
        }

        $inquiries = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function showInquiry(Inquiry $inquiry)
    {
        $inquiry->markAsRead();
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function updateInquiryStatus(Request $request, Inquiry $inquiry)
    {
        $inquiry->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Inquiry status updated.');
    }

    public function deleteInquiry(Inquiry $inquiry)
    {
        $inquiry->delete();
        return redirect()->route('admin.inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }

    public function services(Request $request)
    {
        $services = Service::orderBy('name')->get();
        return view('admin.services.index', compact('services'));
    }

    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $this->sanitizeServiceDuration($request->duration),
            'is_active' => $request->has('is_active'),
            'price' => 0,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/services'), $imageName);
            $data['image'] = 'images/services/' . $imageName;
        }

        Service::create($data);

        return redirect()->back()->with('success', 'Service added successfully!');
    }

    public function updateService(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $this->sanitizeServiceDuration($request->duration),
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($service->image && file_exists(public_path($service->image))) {
                unlink(public_path($service->image));
            }
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/services'), $imageName);
            $data['image'] = 'images/services/' . $imageName;
        }

        $service->update($data);

        return redirect()->back()->with('success', 'Service updated successfully!');
    }

    public function deleteService(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Service deleted.');
    }

    protected function sanitizeServiceDuration(?string $duration): string
    {
        $duration = trim((string) $duration);

        return $duration;
    }

    public function sendReminder(Appointment $appointment)
    {
        Mail::to($appointment->user->email)->send(new AppointmentReminder($appointment));

        Reminder::create([
            'user_id' => $appointment->user_id,
            'appointment_id' => $appointment->id,
            'type' => 'email',
            'send_at' => now(),
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Reminder email sent successfully to ' . $appointment->user->email . '!');
    }

    public function destroyAppointment(Appointment $appointment)
    {
        if (! in_array($appointment->status, ['cancelled', 'rejected'])) {
            return redirect()->back()->with('error', 'Only cancelled or rejected appointments can be deleted.');
        }

        $appointment->delete();

        return redirect()->back()->with('success', ucfirst($appointment->status) . ' appointment deleted successfully.');
    }

    protected function sendAppointmentStatusNotification(Appointment $appointment, string $actionLabel): void
    {
        $recipientEmails = User::admins()
            ->pluck('email')
            ->push($appointment->user->email)
            ->filter()
            ->unique()
            ->values();

        foreach ($recipientEmails as $email) {
            Mail::to($email)->send(new AppointmentStatusNotification($appointment, $actionLabel));
        }
    }

    public function users()
    {
        $users = User::where('role', 'user')->orderBy('name')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function createAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
        ]);

        return redirect()->back()->with('success', 'Admin user created successfully!');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    public function userPets(User $user)
    {
        $pets = $user->pets;
        
        if ($pets->isEmpty()) {
            return '<p class="text-gray-500 text-center py-4">No pets found</p>';
        }
        
        $html = '';
        foreach ($pets as $pet) {
            $html .= '<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">';
            $html .= '<div>';
            $html .= '<p class="font-medium text-gray-900">' . e($pet->name) . ' (' . e($pet->type) . ')</p>';
            $html .= '<p class="text-xs text-gray-500">' . e($pet->breed ?? 'No breed') . '</p>';
            $html .= '</div>';
            $html .= '<button type="button" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-lg hover:bg-red-200" onclick="openDeletePetModal(' . $pet->id . ', \'' . e($pet->name) . '\')">Delete</button>';
            $html .= '</div>';
        }
        
        return $html;
    }

    public function deletePet(Pet $pet)
    {
        $pet->delete();
        
        return redirect()->back()->with('success', 'Pet deleted successfully!');
    }

    public function deleteReminder(Reminder $reminder)
    {
        $reminder->delete();
        
        return redirect()->back()->with('success', 'Reminder deleted successfully!');
    }

    public function feedback(Request $request)
    {
        $query = Feedback::with(['user', 'replies.user'])->parentFeedback()->orderBy('created_at', 'desc');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $feedback = $query->paginate(15);
        return view('admin.feedback.index', compact('feedback'));
    }

    public function updateFeedback(Request $request, Feedback $feedback)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'message' => 'required|string|max:1000',
        ]);

        $feedback->update([
            'rating' => $request->rating,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Feedback updated successfully!');
    }

    public function deleteFeedback(Feedback $feedback)
    {
        $feedback->delete();
        return redirect()->back()->with('success', 'Feedback deleted successfully!');
    }

    public function replyFeedback(Request $request, Feedback $feedback)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'rating' => null,
            'message' => $request->message,
            'parent_id' => $feedback->id,
        ]);

        return redirect()->back()->with('success', 'Reply added successfully!');
    }
}
