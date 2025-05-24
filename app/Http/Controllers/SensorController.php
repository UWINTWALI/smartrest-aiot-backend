<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * Controller for handling sensor data collection and retrieval
 */
class SensorController extends Controller
{
    /**
     * Device uploads batched readings.
     *
     * @OA\Post(
     *     path="/sensors/data",
     *     summary="Store sensor readings",
     *     description="Upload batched sensor readings from smart bed",
     *     operationId="storeSensorData",
     *     tags={"Sensors"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patient_id","bed_id","readings"},
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="bed_id", type="string", example="BED-12345"),
     *             @OA\Property(
     *                 property="readings",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"sensor_type", "sensor_value"},
     *                     @OA\Property(
     *                         property="sensor_type",
     *                         type="string",
     *                         enum={"pressure","heart_rate","breathing_rate","temperature","humidity","body_movement","posture","vibration","sleep_apnea"},
     *                         example="heart_rate"
     *                     ),
     *                     @OA\Property(property="sensor_value", type="number", format="float", example=72.5),
     *                     @OA\Property(property="sensor_unit", type="string", example="bpm"),
     *                     @OA\Property(property="timestamp", type="string", format="date-time", example="2025-05-24T12:00:00Z"),
     *                     @OA\Property(property="additional_metadata", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Data stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Sensor data stored successfully"),
     *             @OA\Property(property="readings_count", type="integer", example=10)
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
    public function storeData(Request $request)
    {
        // Validate the request
        $request->validate([
            'patient_id' => 'required|uuid|exists:patient_profiles,patient_id',
            'bed_id' => 'required|string|max:64',
            'readings' => 'required|array',
            'readings.*.sensor_type' => 'required|in:pressure,heart_rate,breathing_rate,temperature,humidity,body_movement,posture,vibration,sleep_apnea',
            'readings.*.sensor_value' => 'required|numeric',
            'readings.*.sensor_unit' => 'nullable|string|max:20',
            'readings.*.timestamp' => 'nullable|date',
            'readings.*.additional_metadata' => 'nullable|array',
        ]);

        $readings = [];
        
        foreach ($request->readings as $reading) {
            $readings[] = SensorReading::create([
                'patient_id' => $request->patient_id,
                'bed_id' => $request->bed_id,
                'sensor_type' => $reading['sensor_type'],
                'sensor_value' => $reading['sensor_value'],
                'sensor_unit' => $reading['sensor_unit'] ?? null,
                'additional_metadata' => $reading['additional_metadata'] ?? null,
                'timestamp' => $reading['timestamp'] ?? now(),
            ]);
        }
        
        return response()->json([
            'message' => 'Sensor data stored successfully',
            'readings_count' => count($readings)
        ], 201);
    }
    
    /**
     * Get latest snapshot for the requesting user/patient.
     * 
     * @OA\Get(
     *     path="/sensors/latest",
     *     summary="Get latest sensor readings",
     *     description="Get latest readings for each sensor type for the patient",
     *     operationId="getLatestSensorData",
     *     tags={"Sensors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="patient_id",
     *         in="query",
     *         description="Patient ID (required for doctors, not for patients)",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Latest sensor readings",
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="string", format="uuid"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(
     *                 property="readings",
     *                 type="object",
     *                 @OA\Property(
     *                     property="heart_rate",
     *                     type="object",
     *                     @OA\Property(property="value", type="number", example=72.5),
     *                     @OA\Property(property="unit", type="string", example="bpm"),
     *                     @OA\Property(property="timestamp", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(
     *                     property="breathing_rate",
     *                     type="object",
     *                     @OA\Property(property="value", type="number", example=16.8),
     *                     @OA\Property(property="unit", type="string", example="breaths/min"),
     *                     @OA\Property(property="timestamp", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(
     *                     property="temperature",
     *                     type="object",
     *                     @OA\Property(property="value", type="number", example=36.7),
     *                     @OA\Property(property="unit", type="string", example="°C"),
     *                     @OA\Property(property="timestamp", type="string", format="date-time")
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
     *         description="Unauthorized - No access to this patient",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You do not have access to this patient")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient not found"
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
    public function getLatest(Request $request)
    {
        $user = $request->user();
        
        // If user is a patient, get their own data
        if ($user->isPatient()) {
            $patientId = $user->user_id;
        }
        // If user is a doctor, they need to specify a patient
        elseif ($user->isDoctor()) {
            $request->validate([
                'patient_id' => 'required|uuid|exists:patient_profiles,patient_id'
            ]);
            
            // Verify doctor has access to this patient
            $doctorProfile = DoctorProfile::where('doctor_id', $user->user_id)->firstOrFail();
            $hasAccess = $doctorProfile->patients()
                ->where('patient_id', $request->patient_id)
                ->exists();
                
            if (!$hasAccess) {
                return response()->json([
                    'message' => 'You do not have access to this patient'
                ], 403);
            }
            
            $patientId = $request->patient_id;
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Get latest readings for each sensor type
        $latestReadings = SensorReading::where('patient_id', $patientId)
            ->selectRaw('DISTINCT ON (sensor_type) *')
            ->orderByRaw('sensor_type, timestamp DESC')
            ->get();
        
        return response()->json([
            'patient_id' => $patientId,
            'timestamp' => now(),
            'readings' => $latestReadings
        ]);
    }
    
    /**
     * Get historical sensor data for analytics and trends.
     * 
     * @OA\Get(
     *     path="/sensors/history",
     *     summary="Get historical sensor data",
     *     description="Retrieve historical sensor readings with filtering options",
     *     operationId="getSensorHistory",
     *     tags={"Sensors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="patient_id",
     *         in="query",
     *         description="Patient ID (required for doctors, not for patients)",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="sensor_type",
     *         in="query",
     *         description="Filter by sensor type",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"pressure","heart_rate","breathing_rate","temperature","humidity","body_movement","posture","vibration","sleep_apnea"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Start date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="End date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="interval",
     *         in="query",
     *         description="Aggregation interval",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"raw", "minute", "hour", "day"},
     *             default="raw"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historical sensor data",
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="string", format="uuid"),
     *             @OA\Property(
     *                 property="readings",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="sensor_type", type="string", example="heart_rate"),
     *                     @OA\Property(property="sensor_value", type="number", example=72.5),
     *                     @OA\Property(property="sensor_unit", type="string", example="bpm"),
     *                     @OA\Property(property="timestamp", type="string", format="date-time"),
     *                     @OA\Property(property="additional_metadata", type="object")
     *                 )
     *             ),
     *             @OA\Property(property="aggregation", type="string", example="raw"),
     *             @OA\Property(property="period", type="object",
     *                 @OA\Property(property="start", type="string", format="date-time"),
     *                 @OA\Property(property="end", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - No access to this patient",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You do not have access to this patient")
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
    public function getHistory(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'type' => 'nullable|in:pressure,heart_rate,breathing_rate,temperature,humidity,body_movement,posture,vibration,sleep_apnea',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'limit' => 'nullable|integer|min:1|max:1000'
        ]);
        
        // If user is a patient, get their own data
        if ($user->isPatient()) {
            $patientId = $user->user_id;
        }
        // If user is a doctor, they need to specify a patient
        elseif ($user->isDoctor()) {
            $request->validate([
                'patient_id' => 'required|uuid|exists:patient_profiles,patient_id'
            ]);
            
            // Verify doctor has access to this patient
            $doctorProfile = DoctorProfile::where('doctor_id', $user->user_id)->firstOrFail();
            $hasAccess = $doctorProfile->patients()
                ->where('patient_id', $request->patient_id)
                ->exists();
                
            if (!$hasAccess) {
                return response()->json([
                    'message' => 'You do not have access to this patient'
                ], 403);
            }
            
            $patientId = $request->patient_id;
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $query = SensorReading::where('patient_id', $patientId);
        
        // Apply filters
        if ($request->has('type')) {
            $query->where('sensor_type', $request->type);
        }
        
        if ($request->has('from')) {
            $query->where('timestamp', '>=', $request->from);
        }
        
        if ($request->has('to')) {
            $query->where('timestamp', '<=', $request->to);
        }
        
        // Order by timestamp descending
        $query->orderBy('timestamp', 'desc');
        
        // Paginate results
        $limit = $request->limit ?? 100;
        $readings = $query->paginate($limit);
        
        return response()->json($readings);
    }
}
