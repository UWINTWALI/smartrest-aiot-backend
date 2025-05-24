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
     * 
     * @OA\Get(
     *     path="/analytics/sleep-report",
     *     summary="Get sleep report",
     *     description="Retrieve AI-generated sleep report including sleep stages, posture map, and overall sleep score",
     *     operationId="getSleepReport",
     *     tags={"Analytics"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date for the report (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="patient_id",
     *         in="query",
     *         description="Patient ID (required for doctors/admins, not for patients)",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sleep report data",
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="string", format="uuid"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(
     *                 property="sleep_duration",
     *                 type="object",
     *                 @OA\Property(property="total_hours", type="number", example=7.5),
     *                 @OA\Property(property="minutes", type="integer", example=450)
     *             ),
     *             @OA\Property(
     *                 property="sleep_stages",
     *                 type="object",
     *                 @OA\Property(property="awake", type="integer", example=45),
     *                 @OA\Property(property="light", type="integer", example=210),
     *                 @OA\Property(property="deep", type="integer", example=120),
     *                 @OA\Property(property="rem", type="integer", example=75)
     *             ),
     *             @OA\Property(property="sleep_score", type="integer", example=85),
     *             @OA\Property(property="posture_changes", type="integer", example=12),
     *             @OA\Property(property="breathing_events", type="integer", example=2),
     *             @OA\Property(property="avg_heart_rate", type="number", example=68),
     *             @OA\Property(property="notes", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - No access to this patient's data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient profile not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient profile not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/analytics/health-summary",
     *     summary="Get health summary",
     *     description="Retrieve consolidated health vitals and trends with flagged anomalies",
     *     operationId="getHealthSummary",
     *     tags={"Analytics"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="patient_id",
     *         in="query",
     *         description="Patient ID (required for doctors/admins, not for patients)",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days to analyze",
     *         required=false,
     *         @OA\Schema(type="integer", default=7, minimum=1, maximum=90)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Health summary data",
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="string", format="uuid"),
     *             @OA\Property(property="period", type="object",
     *                 @OA\Property(property="start_date", type="string", format="date"),
     *                 @OA\Property(property="end_date", type="string", format="date")
     *             ),
     *             @OA\Property(
     *                 property="vital_trends",
     *                 type="object",
     *                 @OA\Property(
     *                     property="heart_rate",
     *                     type="object",
     *                     @OA\Property(property="avg", type="number", example=68.5),
     *                     @OA\Property(property="min", type="number", example=52),
     *                     @OA\Property(property="max", type="number", example=98),
     *                     @OA\Property(property="trend", type="string", example="stable")
     *                 ),
     *                 @OA\Property(
     *                     property="breathing_rate",
     *                     type="object",
     *                     @OA\Property(property="avg", type="number", example=16.2),
     *                     @OA\Property(property="min", type="number", example=12),
     *                     @OA\Property(property="max", type="number", example=22),
     *                     @OA\Property(property="trend", type="string", example="stable")
     *                 ),
     *                 @OA\Property(
     *                     property="sleep_quality",
     *                     type="object",
     *                     @OA\Property(property="avg_score", type="number", example=82),
     *                     @OA\Property(property="trend", type="string", example="improving")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="anomalies",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="type", type="string", example="elevated_heart_rate"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="detected_at", type="string", format="date-time"),
     *                     @OA\Property(property="severity", type="string", enum={"low", "medium", "high"})
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="recommendations",
     *                 type="array",
     *                 @OA\Items(
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - No access to this patient's data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient profile not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient profile not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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
