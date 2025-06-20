<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    // 🔹 Create user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,manager,employee'
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    // 🔹 Read all users
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    // 🔹 Read single user
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        return response()->json($user);
    }

    // 🔹 Update user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role'     => 'sometimes|required|in:admin,manager,employee'
        ]);

        $user->name  = $validated['name'] ?? $user->name;
        $user->email = $validated['email'] ?? $user->email;
        $user->role  = $validated['role'] ?? $user->role;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    // 🔹 Delete user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
