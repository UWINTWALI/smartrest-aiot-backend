<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        return $query->paginate(15);
    }

    public function show(User $user)
    {
        return $user;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:patient,doctor,customer,admin',
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'phone' => 'nullable|string|max:20',
        ]);

        return User::create($validated);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $user->user_id . ',user_id',
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:patient,doctor,customer,admin',
            'first_name' => 'sometimes|string|max:80',
            'last_name' => 'sometimes|string|max:80',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);
        return $user->refresh();
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }
}
