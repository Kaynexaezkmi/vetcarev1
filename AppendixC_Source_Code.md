# Appendix C: Source Code

This appendix presents selected source code from the **VetCare** system. The code samples highlight the main program flow, core processing logic, database connection configuration, and key modules/classes used in the application.

## 1. Main Program File

**File:** `public/index.php`

This file serves as the main entry point of the Laravel-based VetCare system. It initializes the framework, loads dependencies, and handles incoming HTTP requests.

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
```

The main web routes of the application are defined in `routes/web.php`, where user, appointment, dashboard, reminder, feedback, and administrator functions are connected to their respective controllers.

```php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/history', [AppointmentController::class, 'history'])->name('appointments.history');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments.index');
    Route::put('/appointments/{appointment}/approve', [AdminController::class, 'approveAppointment'])->name('appointments.approve');
});
```

## 2. Core Algorithms or Functions

The following functions implement the main business processes of the VetCare system, particularly appointment scheduling, conflict checking, and dashboard generation.

### 2.1 Appointment Booking Logic

**File:** `app/Http/Controllers/AppointmentController.php`

This function validates user input, checks whether the selected appointment slot is already occupied, creates a pet profile, and stores the appointment record.

```php
public function store(Request $request)
{
    $appointmentTime = $this->normalizeAppointmentTime($request->appointment_time);

    $validator = Validator::make($request->all(), [
        'pet_name' => 'required|string|max:255',
        'pet_type' => 'required|in:Dog,Cat,Bird,Rabbit,Hamster,Fish,Reptile,Other',
        'pet_breed' => 'nullable|string|max:255',
        'service_id' => 'nullable|exists:services,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'reason' => 'nullable|string|required_without:service_id',
    ], [
        'reason.required_without' => 'Please specify your reason for visit.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $exists = Appointment::where('appointment_date', $request->appointment_date)
        ->where('appointment_time', $appointmentTime)
        ->whereIn('status', ['pending', 'approved'])
        ->exists();

    if ($exists) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'This time slot is already booked. Please select another time.');
    }

    $pet = Pet::create([
        'user_id' => Auth::id(),
        'name' => $request->pet_name,
        'type' => $request->pet_type,
        'breed' => $request->pet_breed,
    ]);

    Appointment::create([
        'user_id' => Auth::id(),
        'pet_id' => $pet->id,
        'service_id' => $request->service_id,
        'appointment_date' => $request->appointment_date,
        'appointment_time' => $appointmentTime,
        'reason' => $this->resolveAppointmentReason($request),
        'status' => 'pending',
    ]);

    return redirect()->route('dashboard')
        ->with('success', 'Appointment booked successfully! We will review and confirm your appointment.');
}
```

### 2.2 Appointment Rescheduling Logic

This function allows a user or administrator to move an appointment to a new date and time, while preventing duplicate bookings.

```php
public function reschedule(Request $request, Appointment $appointment)
{
    $isAdmin = Auth::user()->isAdmin();
    $appointmentTime = $request->filled('appointment_time')
        ? $this->normalizeAppointmentTime($request->appointment_time)
        : null;

    if ($appointment->user_id !== Auth::id() && !$isAdmin) {
        abort(403);
    }

    if (!$appointment->canReschedule($isAdmin)) {
        return redirect()->back()->with('error', 'This appointment cannot be rescheduled.');
    }

    $exists = Appointment::where('appointment_date', $request->appointment_date)
        ->where('appointment_time', $appointmentTime)
        ->where('id', '!=', $appointment->id)
        ->whereIn('status', ['pending', 'approved'])
        ->exists();

    if ($exists) {
        return redirect()->back()->withInput()->with('error', 'This time slot is already booked.');
    }

    $appointment->update([
        'appointment_date' => $request->appointment_date,
        'appointment_time' => $appointmentTime,
        'status' => 'pending',
        'rescheduled' => true,
    ]);
}
```

### 2.3 Dashboard Processing Logic

**File:** `app/Http/Controllers/DashboardController.php`

This function prepares the user dashboard by loading upcoming appointments, recent appointments, and calendar events.

```php
protected function userDashboard()
{
    $user = Auth::user();

    $upcomingAppointments = Appointment::with(['pet', 'service'])
        ->where('user_id', $user->id)
        ->whereIn('status', ['pending', 'approved'])
        ->where('appointment_date', '>=', now()->toDateString())
        ->orderBy('appointment_date')
        ->orderBy('appointment_time')
        ->limit(5)
        ->get();

    $recentAppointments = Appointment::with(['pet', 'service'])
        ->where('user_id', $user->id)
        ->orderBy('appointment_date', 'desc')
        ->limit(10)
        ->get();

    $calendarEvents = Appointment::where('user_id', $user->id)
        ->where('appointment_date', '>=', now()->startOfMonth()->toDateString())
        ->where('appointment_date', '<=', now()->endOfMonth()->addMonths(2)->toDateString())
        ->get()
        ->map(function ($apt) {
            $color = match($apt->status) {
                'approved' => '#10b981',
                'pending' => '#f59e0b',
                'completed' => '#3b82f6',
                default => '#6b7280',
            };

            return [
                'id' => $apt->id,
                'title' => $apt->pet->name . ' - ' . ($apt->service ? $apt->service->name : 'Checkup'),
                'start' => $apt->appointment_date->format('Y-m-d') . 'T' . $apt->appointment_time,
                'color' => $color,
                'status' => $apt->status,
            ];
        });

    return view('dashboard.user', compact('upcomingAppointments', 'recentAppointments', 'calendarEvents'));
}
```

## 3. Database Connection Code

**File:** `config/database.php`

The system uses Laravel's database configuration file to define supported database drivers and environment-based connection settings. The MySQL configuration is shown below.

```php
'default' => env('DB_CONNECTION', 'sqlite'),

'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'url' => env('DB_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'laravel'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
    ],
],
```

This configuration allows the application to connect to the database securely using values stored in the environment file (`.env`), instead of hardcoding credentials in the source code.

## 4. Key Modules/Classes

The following classes form the core modules of the VetCare system.

### 4.1 Appointment Model

**File:** `app/Models/Appointment.php`

This model stores appointment data and includes relationships, status helpers, query scopes, and slot-blocking logic.

```php
class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'service_id',
        'appointment_date',
        'appointment_time',
        'reason',
        'status',
        'rescheduled',
        'cancellation_reason',
        'cancelled_by',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'rescheduled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function canReschedule($isAdmin = false): bool
    {
        if ($isAdmin) {
            return in_array($this->status, ['pending', 'approved']) && !$this->rescheduled;
        }

        return $this->status === 'pending' && !$this->rescheduled;
    }
}
```

### 4.2 Pet Model

**File:** `app/Models/Pet.php`

This model manages pet information and its relationships with owners, appointments, and medical records.

```php
class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'breed',
        'gender',
        'date_of_birth',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
```

### 4.3 Dashboard Controller

**File:** `app/Http/Controllers/DashboardController.php`

This controller determines whether the logged-in account is a regular user or an administrator, then loads the proper dashboard data and reminder information.

```php
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->userDashboard();
    }

    public function reminders()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $reminders = Reminder::with(['appointment.pet', 'appointment.user'])
                ->orderBy('send_at')
                ->paginate(15);
        } else {
            $reminders = Reminder::with(['appointment.pet', 'appointment.service'])
                ->where('user_id', $user->id)
                ->orderBy('send_at')
                ->paginate(15);
        }

        return view('dashboard.reminders', compact('reminders'));
    }
}
```

### 4.4 Admin Controller

**File:** `app/Http/Controllers/AdminController.php`

This controller handles administrative operations such as appointment approval, patient record management, inquiry monitoring, service maintenance, and user management.

```php
class AdminController extends Controller
{
    public function approveAppointment(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        Reminder::create([
            'user_id' => $appointment->user_id,
            'appointment_id' => $appointment->id,
            'type' => 'email',
            'send_at' => now(),
            'is_sent' => false,
        ]);

        return redirect()->back()->with('success', 'Appointment approved successfully!');
    }

    public function storeMedicalRecord(Request $request, Pet $pet)
    {
        MedicalRecord::create([
            'pet_id' => $pet->id,
            'title' => 'Medical Record - ' . $request->record_date,
            'notes' => $request->notes,
            'record_date' => $request->record_date,
            'created_by' => Auth::id(),
            'submission_token' => $request->submission_token,
        ]);

        return redirect()->back()->with('success', 'Medical record added successfully!');
    }
}
```

## Summary

The source code in this appendix shows that the VetCare system is structured using the Laravel MVC architecture:

1. `public/index.php` serves as the application entry point.
2. Controllers such as `AppointmentController`, `DashboardController`, and `AdminController` implement the core business logic.
3. `config/database.php` defines the database connectivity settings.
4. Models such as `Appointment` and `Pet` encapsulate the main data entities and relationships of the system.

These code components collectively support the appointment scheduling, pet record management, reminder handling, and administrative monitoring features of the VetCare application.
