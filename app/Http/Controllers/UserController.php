<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

/**
 * Controller for user management operations
 */
class UserController extends Controller
{
    /**
     * List users with filters (Admin).
     *
     * @OA\Get(
     *     path="/users",
     *     summary="List all users",
     *     description="Returns paginated list of users (Admin only)",
     *     operationId="userIndex",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter by user role",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"patient", "doctor", "admin", "customer"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in name or email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Admin access required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $query = User::query();
        
        // Apply filters if provided
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate();
    }
      /**
     * Get user by ID (Admin, or self).
     *
     * @OA\Get(
     *     path="/users/{userId}",
     *     summary="Get user details",
     *     description="Returns user details by ID (Admin or self access only)",
     *     operationId="userShow",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized access",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function show(Request $request, $userId)
    {
        $requestingUser = $request->user();
        
        // If not admin and not the user themselves
        if (!$requestingUser->isAdmin() && $requestingUser->user_id != $userId) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $user = User::where('user_id', $userId)->firstOrFail();
        
        // Load the appropriate profile based on user role
        if ($user->isPatient()) {
            $user->load('patientProfile');
        } elseif ($user->isDoctor()) {
            $user->load('doctorProfile');
        }
        
        return response()->json($user);
    }
      /**
     * Create user manually (Admin onboarding).
     * 
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     description="Create a new user manually (Admin only)",
     *     operationId="userStore",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data",
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","role"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"patient", "doctor", "customer", "admin"}),
     *             @OA\Property(property="license_no", type="string", example="DOC12345"),
     *             @OA\Property(property="specialty", type="string", example="Cardiology"),
     *             @OA\Property(property="national_id", type="string", example="ID12345678"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="sex", type="string", enum={"M", "F", "O"}, example="M")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Admin access required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'required|string|email|max:80|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:patient,doctor,customer,admin',
            // Role-specific fields
            'license_no' => 'required_if:role,doctor',
            'specialty' => 'nullable|string|max:60',
            'national_id' => 'nullable|string|max:16',
            'date_of_birth' => 'nullable|date',
            'sex' => 'nullable|in:M,F,O',
        ]);
        
        DB::beginTransaction();
        try {
            // Create the user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
            
            // Create profile based on role
            if ($request->role === 'patient') {
                PatientProfile::create([
                    'patient_id' => $user->user_id,
                    'national_id' => $request->national_id,
                    'date_of_birth' => $request->date_of_birth,
                    'sex' => $request->sex,
                ]);
            } elseif ($request->role === 'doctor') {
                DoctorProfile::create([
                    'doctor_id' => $user->user_id,
                    'license_no' => $request->license_no,
                    'specialty' => $request->specialty,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'User creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
      /**
     * Update profile / role (Admin; self‑update allowed on own record).
     * 
     * @OA\Put(
     *     path="/users/{userId}",
     *     summary="Update user details",
     *     description="Update an existing user (Admin or self access only)",
     *     operationId="userUpdate",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Smith"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="role", type="string", enum={"patient", "doctor", "customer", "admin"}),
     *             @OA\Property(property="license_no", type="string", example="DOC12345"),
     *             @OA\Property(property="specialty", type="string", example="Cardiology"),
     *             @OA\Property(property="national_id", type="string", example="ID12345678"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="sex", type="string", enum={"M", "F", "O"}, example="M")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized access",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function update(Request $request, $userId)
    {
        $requestingUser = $request->user();
        $user = User::where('user_id', $userId)->firstOrFail();
        
        // If not admin and not the user themselves
        if (!$requestingUser->isAdmin() && $requestingUser->user_id != $userId) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Only admins can change roles
        if ($request->has('role') && !$requestingUser->isAdmin()) {
            return response()->json([
                'message' => 'Only admins can change user roles'
            ], 403);
        }
        
        $rules = [
            'first_name' => 'sometimes|string|max:80',
            'last_name' => 'sometimes|string|max:80',
            'email' => 'sometimes|string|email|max:80|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
        ];
        
        // Admin-only rules
        if ($requestingUser->isAdmin()) {
            $rules['role'] = 'sometimes|in:patient,doctor,customer,admin';
        }
        
        $request->validate($rules);
        
        DB::beginTransaction();
        try {
            // Update user data
            $user->update($request->only([
                'first_name', 'last_name', 'email', 'phone', 'role'
            ]));
            
            DB::commit();
            
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'User update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
      /**
     * Deactivate or hard‑delete user (Admin).
     * 
     * @OA\Delete(
     *     path="/users/{userId}",
     *     summary="Delete a user",
     *     description="Deactivate or hard delete a user (Admin only)",
     *     operationId="userDestroy",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Admin access required",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function destroy(Request $request, $userId)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $user = User::where('user_id', $userId)->firstOrFail();
        
        // Cannot delete yourself
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'Cannot delete your own account'
            ], 400);
        }
        
        $user->delete();
        
        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
