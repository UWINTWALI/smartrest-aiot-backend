<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * AI‑generated nightly summary (sleep stages, posture map, score).
     */
    public function getSleepReport(Request $request)
    {
        $user = $request->user();
        
        // Validate request
        $request->validate([
            'date' => 'nullable|date',
        ]);
        
        $date = $request->date ?? now()->format('Y-m-d');
        
        // If user is a patient, get their own data
        if ($user->isPatient()) {
            $patientId = $user->user_id;
        }
        // If user is a doctor, they need to specify a patient
        elseif ($user->isDoctor() || $user->isAdmin()) {
            $request->validate([
                'patient_id' => 'required|uuid|exists:patient_profiles,patient_id'
            ]);
            
            // For doctors, verify access to the patient
            if ($user->isDoctor()) {
                $doctorProfile = DoctorProfile::where('doctor_id', $user->user_id)->firstOrFail();
                $hasAccess = $doctorProfile->patients()
                    ->where('patient_id', $request->patient_id)
                    ->exists();
                    
                if (!$hasAccess) {
                    return response()->json([
                        'message' => 'You do not have access to this patient'
                    ], 403);
                }
            }
            
            $patientId = $request->patient_id;
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Get the patient profile
        $patient = PatientProfile::where('patient_id', $patientId)->first();
        if (!$patient) {
            return response()->json([
                'message' => 'Patient profile not found'
            ], 404);
        }
        
        // Get relevant sensor readings for the date
        $startDate = $date . ' 00:00:00';
        $endDate = $date . ' 23:59:59';
        
        $readings = SensorReading::where('patient_id', $patientId)
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->orderBy('timestamp')
            ->get();
            
        // This would typically be replaced with actual AI analysis
        // For now, return simulated data
        $sleepReport = [
            'patient_id' => $patientId,
            'date' => $date,
            'sleep_duration' => [
                'hours' => 7,
                'minutes' => 42
            ],
            'sleep_stages' => [
                'deep' => 120, // minutes
                'light' => 240,
                'rem' => 102,
                'awake' => 0
            ],
            'sleep_score' => 85,
            'posture_changes' => 12,
            'breathing_events' => 2,
            'avg_heart_rate' => 68,
            'notes' => 'Sleep quality is good. No significant issues detected.',
        ];
        
        return response()->json($sleepReport);
    }
    
    /**
     * Consolidated vitals trends & flagged anomalies.
     */
    public function getHealthSummary(Request $request)
    {
        $user = $request->user();
        
        // Validate request
        $request->validate([
            'days' => 'nullable|integer|min:1|max:90',
        ]);
        
        $days = $request->days ?? 7;
        
        // If user is a patient, get their own data
        if ($user->isPatient()) {
            $patientId = $user->user_id;
        }
        // If user is a doctor, they need to specify a patient
        elseif ($user->isDoctor() || $user->isAdmin()) {
            $request->validate([
                'patient_id' => 'required|uuid|exists:patient_profiles,patient_id'
            ]);
            
            // For doctors, verify access to the patient
            if ($user->isDoctor()) {
                $doctorProfile = DoctorProfile::where('doctor_id', $user->user_id)->firstOrFail();
                $hasAccess = $doctorProfile->patients()
                    ->where('patient_id', $request->patient_id)
                    ->exists();
                    
                if (!$hasAccess) {
                    return response()->json([
                        'message' => 'You do not have access to this patient'
                    ], 403);
                }
            }
            
            $patientId = $request->patient_id;
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Get the patient profile
        $patient = PatientProfile::where('patient_id', $patientId)->first();
        if (!$patient) {
            return response()->json([
                'message' => 'Patient profile not found'
            ], 404);
        }
        
        // Get relevant sensor readings for the time period
        $startDate = now()->subDays($days)->startOfDay();
        
        $readings = SensorReading::where('patient_id', $patientId)
            ->where('timestamp', '>=', $startDate)
            ->orderBy('timestamp')
            ->get();
            
        // This would typically be replaced with actual AI analysis
        // For now, return simulated data
        $healthSummary = [
            'patient_id' => $patientId,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => now()->toDateString(),
                'days' => $days
            ],
            'vitals' => [
                'heart_rate' => [
                    'avg' => 72,
                    'min' => 58,
                    'max' => 110,
                    'trend' => 'stable'
                ],
                'breathing_rate' => [
                    'avg' => 14,
                    'min' => 12,
                    'max' => 18,
                    'trend' => 'stable'
                ],
                'body_temperature' => [
                    'avg' => 36.8,
                    'min' => 36.5,
                    'max' => 37.1,
                    'trend' => 'stable'
                ]
            ],
            'anomalies' => [
                [
                    'type' => 'tachycardia',
                    'detected_at' => now()->subDays(2)->setTime(3, 15)->toIso8601String(),
                    'duration_minutes' => 12,
                    'severity' => 'mild',
                    'description' => 'Brief elevated heart rate detected during sleep'
                ]
            ],
            'sleep_apnea' => [
                'index' => 1.2, // events per hour
                'severe_events' => 0,
                'trend' => 'improving'
            ],
            'recommendations' => [
                'Continue regular sleep schedule',
                'Consider follow-up for occasional heart rate elevation'
            ]
        ];
        
        return response()->json($healthSummary);
    }
}
