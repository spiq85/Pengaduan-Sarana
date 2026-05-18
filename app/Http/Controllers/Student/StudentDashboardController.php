<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\InputAspirations;
use App\Models\Aspirations;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Student::findOrFail(Auth::guard('student')->id());

        $total = $student->inputAspirations()->count();
        $diterima = $student->inputAspirations()
            ->where('submission_status', 'diterima')
            ->count();

        $menunggu = $student->inputAspirations()
            ->where('submission_status', 'menunggu')
            ->count();

        $recentAspirations = InputAspirations::with(['category', 'aspiration'])
            ->where('input_by', $student->id_student)
            ->latest('input_at')
            ->take(5)
            ->get();

        return Inertia::render('Student/Dashboard', [
            'student' => $student,
            'total' => $total,
            'diterima' => $diterima,
            'menunggu' => $menunggu,
            'recentAspirations' => $recentAspirations,
        ]);
    }

    public function profile()
    {
        $student = Student::findOrFail(Auth::guard('student')->id());

        [$stats, $badges] = $this->buildStudentStatsAndBadges($student);

        return Inertia::render('Student/Profile', [
            'student' => $student,
            'stats' => $stats,
            'badges' => $badges,
            'studentAspirations' => [],
            'isOwnProfile' => true,
        ]);
    }

    public function showStudentProfile(Student $student)
    {
        [$stats, $badges] = $this->buildStudentStatsAndBadges($student);

        $studentAspirations = $student->inputAspirations()
            ->with('category')
            ->latest('input_at')
            ->take(8)
            ->get();

        return Inertia::render('Student/Profile', [
            'student' => $student,
            'stats' => $stats,
            'badges' => $badges,
            'studentAspirations' => $studentAspirations,
            'isOwnProfile' => Auth::guard('student')->id() === $student->id_student,
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
            ->whereHas('input', function ($query) {
                $query->where('submission_mode', 'template');
            })
            // Ambil semua kolom aspirations + alias end_at sebagai deadline untuk Vue
            ->select('aspirations.*', 'aspirations.end_at as deadline')
            ->withCount('votes')
            ->withExists(['votes as user_has_voted' => function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            }])
            ->latest()
            ->get();

        $studentIds = collect($aspirations)
            ->flatMap(function ($aspiration) {
                $ids = [];

                if ($aspiration->student?->id_student) {
                    $ids[] = $aspiration->student->id_student;
                }

                foreach ($aspiration->comments ?? [] as $comment) {
                    if ($comment->student?->id_student) {
                        $ids[] = $comment->student->id_student;
                    }
                }

                return $ids;
            })
            ->unique()
            ->values();

        $badgeMap = $this->buildBadgeMapForStudents($studentIds);

        $aspirations->each(function ($aspiration) use ($badgeMap) {
            if ($aspiration->student?->id_student) {
                $aspiration->student->setAttribute(
                    'special_badges',
                    $badgeMap[$aspiration->student->id_student] ?? [],
                );
            }

            foreach ($aspiration->comments ?? [] as $comment) {
                if ($comment->student?->id_student) {
                    $comment->student->setAttribute(
                        'special_badges',
                        $badgeMap[$comment->student->id_student] ?? [],
                    );
                }
            }
        });

        return Inertia::render('Student/GlobalDashboard', [
            'student' => auth()->guard('student')->user(),
            'aspirations' => $aspirations,
        ]);
    }

    private function buildStudentStatsAndBadges(Student $student): array
    {
        $total = $student->inputAspirations()->count();
        $pending = $student->inputAspirations()
            ->where('submission_status', 'menunggu')
            ->count();
        $supports = $student->votesGiven()->count();

        return [[
            'total' => $total,
            'pending' => $pending,
            'supports' => $supports,
        ], $this->calculateSpecialBadges($total, $supports)];
    }

    private function buildBadgeMapForStudents(Collection $studentIds): array
    {
        if ($studentIds->isEmpty()) {
            return [];
        }

        $students = Student::query()
            ->whereIn('id_student', $studentIds)
            ->withCount([
                'inputAspirations as aspirations_count',
                'votesGiven as supports_count',
            ])
            ->get();

        $map = [];
        foreach ($students as $student) {
            $map[$student->id_student] = $this->calculateSpecialBadges(
                (int) $student->aspirations_count,
                (int) $student->supports_count,
            );
        }

        return $map;
    }

    private function calculateSpecialBadges(int $aspirationCount, int $supportCount): array
    {
        $badges = [];

        if ($aspirationCount >= 8) {
            $badges[] = [
                'key' => 'si-paling-aspirasi',
                'label' => 'Si Paling Aspirasi',
                'icon' => 'fa-bullhorn',
                'theme' => 'blue',
            ];
        }

        if ($supportCount >= 12) {
            $badges[] = [
                'key' => 'si-paling-support',
                'label' => 'Si Paling Support',
                'icon' => 'fa-thumbs-up',
                'theme' => 'amber',
            ];
        }

        if (empty($badges) && $aspirationCount >= 3) {
            $badges[] = [
                'key' => 'kontributor-aktif',
                'label' => 'Kontributor Aktif',
                'icon' => 'fa-seedling',
                'theme' => 'emerald',
            ];
        }

        return $badges;
    }
}
