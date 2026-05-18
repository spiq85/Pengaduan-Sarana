<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Student;
use Inertia\Inertia;
use App\Models\PasswordResetRequest;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Student/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('student')->attempt($credentials)) {
            $request->session()->regenerate();
            // Tambahkan flash message sukses
            return redirect()->route('student.dashboard')->with('message', 'Selamat datang kembali!');
        }

        // Throw 422 agar Inertia trigger onError di frontend
        throw ValidationException::withMessages([
            'username' => 'Username atau password salah.',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:students,nis',
            'message' => 'required|string|max:500',
        ]);

        $student = Student::where('nis', $request->nis)->first();

        $existing = PasswordResetRequest::where('id_student', $student->id_student)
            ->where('status', 'pending')
            ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'nis' => 'Permintaan reset password kamu sudah dikirim. Tunggu admin memprosesnya.'
                ]);
            }

            PasswordResetRequest::create([
                'id_student' => $student->id_student,
                'message' => $request->message,
            ]);

            return back()->with('success', 'Permintaan reset password berhasil dikirim ke admin.');
    }
}
