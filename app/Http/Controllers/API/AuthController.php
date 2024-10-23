<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user and return an access token.
     */
    public function register(Request $request)
    {
        // Validasi input dari pengguna
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
        ]);

        // Buat token untuk pengguna baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // Kembalikan token dalam respons JSON
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201); // Kode status 201 untuk sukses pembuatan
    }

    /**
     * Log in an existing user and return an access token.
     */
    public function login(Request $request)
    {
        // Validasi input dari pengguna
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari pengguna berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Periksa apakah pengguna ditemukan dan apakah password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Buat token untuk pengguna yang berhasil login
        $token = $user->createToken('auth_token')->plainTextToken;

        // Kembalikan token dalam respons JSON
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Log out the authenticated user.
     */
    public function logout(Request $request)
    {
        // Hapus token akses saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get all users.
     */
    public function index()
    {
        // Ambil semua data dari tabel users
        $users = User::all();

        // Kembalikan data dalam format JSON
        return response()->json($users);
    }

    /**
     * Get user data by ID.
     */
    public function getUserData($id)
    {
        // Ambil data pengguna berdasarkan ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Kembalikan data pengguna dalam format JSON
        return response()->json($user);
    }
}
