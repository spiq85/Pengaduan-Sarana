<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\InputAspirations;
use App\Models\Aspirations;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();

        $total = $student->inputAspirations()->count();
        $diterima = $student->inputAspirations()
            ->where('submission_status', 'diterima')
            ->count();

        $menunggu = $student->inputAspirations()
            ->where('submission_status', 'menunggu')
            ->count();

        return Inertia::render('Student/Dashboard', [
            'student' => $student,
            'total' => $total,
            'diterima' => $diterima,
            'menunggu' => $menunggu,
        ]);
    }

    public function profile()
    {
        $student = Auth::guard('student')->user();
        
        return Inertia::render('Student/Profile', [
            'student' => $student,
            'stats' => [
                'total' => $student->inputAspirations()->count(),
                'pending' => $student->inputAspirations()->where('submission_status', 'menunggu')->count(),
            ]
        ]);
    }

    public function global()
    {
        $studentId = auth()->guard('student')->id();

        // Query Anti-Duplikasi & Timer Enabled
        $aspirations = Aspirations::query()
            ->with([
                'student',
                'category',
                'input',
                'comments.student'
            ])
            // Ambil semua kolom aspirations + alias end_at sebagai deadline untuk Vue
            ->select('aspirations.*', 'aspirations.end_at as deadline')
            ->withCount('votes')
            ->withExists(['votes as user_has_voted' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }])
            // Kunci utama agar tidak duplikat saat join votes/comments
            ->groupBy('aspirations.id_aspiration')
            ->latest()
            ->get();

        return Inertia::render('Student/GlobalDashboard', [
            'student' => auth()->guard('student')->user(),
            'aspirations' => $aspirations,
        ]);
    }
}
