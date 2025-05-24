<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * Get uptime, firmware version, sensor health of a mattress unit.
     */
    public function getStatus(Request $request)
    {
        $request->validate([
            'bed_id' => 'required|string|max:64'
        ]);
        
        $user = $request->user();
        $bedId = $request->bed_id;
        
        // Only Admin and Doctors can access this endpoint
        if (!$user->isAdmin()) {
            if ($user->isDoctor()) {
                // For doctors, verify they have a patient using this bed
                $doctorProfile = DoctorProfile::where('doctor_id', $user->user_id)->firstOrFail();
                
                // This is a simplified check - in a real system, you'd check if the bed belongs to any of the doctor's patients
                $hasAccess = false;
                
                if (!$hasAccess) {
                    return response()->json([
                        'message' => 'You do not have access to this device'
                    ], 403);
                }
            } else {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
        }
        
        // Get the latest system logs
        $logs = SystemLog::where('bed_id', $bedId)
            ->orderBy('logged_at', 'desc')
            ->limit(10)
            ->get();
        
        // This would typically fetch real-time data from the device
        // For now, return simulated data
        $statusData = [
            'bed_id' => $bedId,
            'status' => 'online',
            'uptime' => '3d 7h 22m',
            'firmware' => [
                'version' => 'v2.3.1',
                'last_updated' => '2025-05-10T14:30:00Z'
            ],
            'battery' => [
                'level' => 82,
                'estimated_remaining' => '4d 12h'
            ],
            'sensors' => [
                [
                    'type' => 'pressure',
                    'status' => 'ok',
                    'last_reading' => now()->subMinutes(5)->toIso8601String()
                ],
                [
                    'type' => 'heart_rate',
                    'status' => 'ok',
                    'last_reading' => now()->subMinutes(2)->toIso8601String()
                ],
                [
                    'type' => 'temperature',
                    'status' => 'ok',
                    'last_reading' => now()->subMinutes(10)->toIso8601String()
                ]
            ],
            'connectivity' => [
                'type' => 'wifi',
                'strength' => 'good',
                'last_sync' => now()->subMinutes(1)->toIso8601String()
            ],
            'recent_logs' => $logs
        ];
        
        return response()->json($statusData);
    }
    
    /**
     * Trigger remote restart / soft reset of device.
     */
    public function reboot(Request $request)
    {
        $request->validate([
            'bed_id' => 'required|string|max:64'
        ]);
        
        $user = $request->user();
        $bedId = $request->bed_id;
        
        // Only Admin can reboot devices
        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized - only admins can reboot devices'
            ], 403);
        }
        
        // Log the reboot command
        SystemLog::create([
            'bed_id' => $bedId,
            'severity' => 'INFO',
            'message' => 'Remote reboot initiated by admin ' . $user->email,
            'logged_at' => now()
        ]);
        
        // This would typically send a command to the device
        // For now, just simulate a response
        
        return response()->json([
            'message' => 'Reboot command sent successfully',
            'bed_id' => $bedId,
            'reboot_initiated_at' => now()->toIso8601String(),
            'estimated_completion' => now()->addMinutes(2)->toIso8601String(),
        ]);
    }
}
