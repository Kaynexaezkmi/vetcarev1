<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f97316;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #fff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .appointment-card {
            background-color: #fff7ed;
            border: 2px solid #fed7aa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .appointment-card h3 {
            color: #ea580c;
            margin-top: 0;
        }
        .detail-row {
            display: flex;
            margin: 10px 0;
        }
        .detail-label {
            font-weight: bold;
            width: 120px;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .message {
            margin: 20px 0;
            padding: 15px;
            background-color: #f3f4f6;
            border-left: 4px solid #f97316;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            background-color: #f97316;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">VetCare Reminder</h1>
        <p style="margin: 5px 0 0 0;">Your pet's appointment is coming up!</p>
    </div>
    
    <div class="content">
        <p class="greeting">Dear {{ $appointment->user->name }},</p>
        
        <div class="message">
            <p>This is a friendly reminder about your upcoming pet appointment. Please make sure to arrive on time with your pet.</p>
        </div>
        
        <div class="appointment-card">
            <h3>Appointment Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Pet Name:</span>
                <span class="detail-value">{{ $appointment->pet->name ?? 'N/A' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Service:</span>
                <span class="detail-value">{{ $appointment->service ? $appointment->service->name : 'General Checkup' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F j, Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</span>
            </div>
            
            @if($appointment->reason)
            <div class="detail-row">
                <span class="detail-label">Reason:</span>
                <span class="detail-value">{{ $appointment->reason }}</span>
            </div>
            @endif
        </div>
        
        <p><strong>Please remember to:</strong></p>
        <ul>
            <li>Arrive 10-15 minutes before your scheduled appointment time</li>
            <li>Bring any relevant medical records or vaccination certificates</li>
            <li>Keep your pet calm and comfortable during the visit</li>
        </ul>
        
        <p>If you need to reschedule or cancel your appointment, please contact us as soon as possible.</p>
        
        <p>Thank you for choosing VetCare for your pet's healthcare needs!</p>
        
        <p>Warm regards,<br><strong>The VetCare Team</strong></p>
        
        <div class="footer">
            <p>VetCare Veterinary Clinic<br>
            Your Trusted Partner in Pet Health</p>
        </div>
    </div>
</body>
</html>
