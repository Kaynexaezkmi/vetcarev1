<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment {{ $actionLabel }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6; margin: 0; padding: 24px; background: #f9fafb;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #e5e7eb;">
        <h2 style="margin-top: 0; color: #111827;">Appointment {{ $actionLabel }}</h2>
        <p>Hello,</p>
        <p>
            This is to confirm that the appointment for <strong>{{ $appointment->pet->name }}</strong>
            has been <strong>{{ strtolower($actionLabel) }}</strong>.
        </p>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin: 20px 0;">
            <p style="margin: 0 0 8px;"><strong>Pet Owner:</strong> {{ $appointment->user->name }}</p>
            <p style="margin: 0 0 8px;"><strong>Service:</strong> {{ $appointment->service?->name ?? 'General Checkup' }}</p>
            <p style="margin: 0 0 8px;"><strong>Date:</strong> {{ $appointment->appointment_date->format('M d, Y') }}</p>
            <p style="margin: 0 0 8px;"><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
            <p style="margin: 0 0 8px;"><strong>Original Concern:</strong> {{ $appointment->reason }}</p>
            <p style="margin: 0 0 8px;"><strong>Updated Status:</strong> {{ $appointment->status_label }}</p>
            <p style="margin: 0;"><strong>Reason:</strong> {{ $appointment->cancellation_reason ?? 'No reason provided.' }}</p>
        </div>

        <p>
            Action taken by:
            <strong>{{ $appointment->cancelled_by === 'admin' ? 'VetCare Admin' : 'Pet Owner' }}</strong>
        </p>

        <p style="margin-bottom: 0;">VetCare</p>
    </div>
</body>
</html>
