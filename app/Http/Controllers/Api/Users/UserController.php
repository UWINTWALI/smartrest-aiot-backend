<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware is handled by route
    }

    // Get all users
    public function index()
    {
        if (!Auth::user()->hasPermission(permission: 'view_users')) {
            return response()->json([
                'message' => 'Access Denied: You do not have permission to manage users',
                'error' => 'insufficient_permissions'
            ], 403);
        }

        $users = User::all();
        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
            'count' => $users->count()
        ]);
    }

    // Show user by ID
    public function show(User $user)
    {
        if (!Auth::user()->hasPermission('view_users')) {
            return response()->json([
                'message' => 'Access Denied: You do not have permission to view user details',
                'error' => 'insufficient_permissions'
            ], 403);
        }

        return response()->json([
            'message' => 'User details retrieved successfully',
            'data' => $user
        ]);
    }

    // Update user
    public function update(Request $request, User $user)
    {
        if (!Auth::user()->hasPermission('update_user')) {
            return response()->json([
                'message' => 'Access Denied: You do not have permission to update users',
                'error' => 'insufficient_permissions'
            ], 403);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:80',
            'last_name' => 'sometimes|string|max:80',
            'email' => 'sometimes|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|in:' . implode(',', array_keys(Config::get('permissions.roles'))),
            'is_email_verified' => 'sometimes|boolean',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string'
        ]);

        if (isset($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    // Delete user
    public function destroy(User $user)
    {
        if (!Auth::user()->hasPermission('delete_user')) {
            return response()->json([
                'message' => 'Access Denied: You do not have permission to delete users',
                'error' => 'insufficient_permissions'
            ], 403);
        }

        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully',
            'status' => 'success'
        ]);
    }

    // Add permission to user
    public function addPermission(Request $request, User $user)
    {
        if (!Auth::user()->hasPermission('manage_permissions')) {
            return response()->json([
                'message' => 'Access Denied: You do not have permission to manage permissions',
                'error' => 'insufficient_permissions'
            ], 403);
        }

        $validated = $request->validate([
            'permission' => 'required_without:permissions|string',
            'permissions' => 'required_without:permission|array',
            'permissions.*' => 'string'
        ]);

        $permissions = [];
        if (isset($validated['permission'])) {
            $permissions = [$validated['permission']];
        } else {
            $permissions = $validated['permissions'];
        }

        $addedPermissions = [];
        $failedPermissions = [];

        foreach ($permissions as $permission) {
            if ($user->addPermission($permission)) {
                $addedPermissions[] = $permission;
            } else {
                $failedPermissions[] = $permission;
            }
        }

        $response = [
            'message' => 'Permission operation completed',
            'data' => $user
        ];

        if (!empty($addedPermissions)) {
            $response['added_permissions'] = $addedPermissions;
        }

        if (!empty($failedPermissions)) {
            $response['failed_permissions'] = $failedPermissions;
            $response['message'] = 'Some permissions could not be added. They might already exist or be part of the user\'s role.';
        }

        return response()->json($response, empty($failedPermissions) ? 200 : 207);
    }

    // Remove permission from user
    public function removePermission(Request $request, User $user)
    {
        if (!Auth::user()->hasPermission('manage_permissions')) {
            return response()->json([
                'message' => 'Access Denied: You do not have permission to manage permissions',
                'error' => 'insufficient_permissions'
            ], 403);
        }

        $validated = $request->validate([
            'permission' => 'required|string'
        ]);

        $user->removePermission($validated['permission']);

        return response()->json([
            'message' => 'Permission removed successfully',
            'data' => $user
        ]);
    }
}