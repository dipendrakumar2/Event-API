<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use  Illuminate\Validation\Validator;
use Exception;
use Illuminate\Validation\Rule;
class AuthController extends Controller
{
    /**
     * Register an user.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }

    /**
     * Login an user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    /**
     * Logout an user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
    /**
     * Update user.
     */
    public function update(Request $request)
    {

        $userId = $request->id;
        $user = User::findOrFail($userId);
        // Validate the data submitted by user
        
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:225|' . Rule::unique('users')->ignore($userId),
        ]);
        try {
            $user->fill([
                'name' => $request->name,
                'email' => $request->email
            ]);

            // update user to database
            $user->save();
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update user', 'message' => $e->getMessage()], 500);
        }
        return response()->json($user, 201);
    }
    /**
     * delete user.
     */
    public function delete(Request $request)
    {

        $userId = $request->id;
        $user = User::findOrFail($userId);
        // Validate the data submitted by user
        
        
        try {
            $user->delete();

        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete user', 'message' => $e->getMessage()], 500);
        }
        return response()->json($user, 201);
    }
}
