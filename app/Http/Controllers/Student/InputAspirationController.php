<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InputAspirations;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;    
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Notifications\NewAspirationForAdminNotification;

class InputAspirationController extends Controller
{
    public function index(Request $request)
    {
        $aspirations = InputAspirations::with([
                'category',
                'aspiration.feedbacks.user.roles',
                'aspiration.validator',
            ])
            ->where('input_by', Auth::guard('student')->id())
            ->latest()
            ->get();

        $customOrderIds = InputAspirations::query()
            ->where('submission_mode', 'custom')
            ->orderBy('input_at')
            ->pluck('id_input')
            ->values();

        $customPriorityMap = $customOrderIds
            ->flip()
            ->map(fn ($idx) => $idx + 1)
            ->all();

        $aspirations->each(function (InputAspirations $aspiration) use ($customPriorityMap) {
            if ($aspiration->submission_mode === 'custom') {
                $aspiration->setAttribute('custom_priority_rank', $customPriorityMap[$aspiration->id_input] ?? null);
            }
        });

        $selectedId = $request->query('selected');

        return Inertia::render('Student/InputAspirations/Index', [
            'aspirations' => $aspirations,
            'selectedId' => $selectedId ? (int) $selectedId : null,
            'categories' => Category::all(['id_category', 'category_name']),
            'locations' => Location::where('is_active', true)
                ->orderBy('location_name')
                ->get(['id_location', 'location_name', 'location_type']),
        ]);
    }

    public function create()
    {
        return redirect()->route('student.input-aspirations.index', ['new' => 1]);
    }

    public function store(Request $request)
    {
        // 1. COOLDOWN: 1 siswa hanya boleh submit 1 aspirasi per hari
        $alreadySubmittedToday = InputAspirations::where('input_by', Auth::guard('student')->id())
            ->where(function($q) {
                $q->whereDate('input_at', today())
                  ->orWhereDate('created_at', today());
            })
            ->exists();

        if ($alreadySubmittedToday) {
            throw ValidationException::withMessages([
                'cooldown' => 'Satu siswa hanya bisa kirim aspirasi sekali dalam sehari.',
            ]);
        }

        $request->validate([
            'submission_mode' => 'required|in:template,custom',
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $isTemplate = $request->submission_mode === 'template';

        if ($isTemplate) {
            $request->validate([
                'id_category' => 'required|exists:categories,id_category',
                'id_location' => 'required|exists:locations,id_location',
                'room_number' => 'nullable|string|max:50',
            ]);
        } else {
            $request->validate([
                'custom_location' => 'required|string|max:120',
            ]);
        }

        $locationRef = null;
        $categoryId = null;
        $roomNumber = null;
        $location = null;

        if ($isTemplate) {
            $locationRef = Location::where('is_active', true)
                ->findOrFail($request->id_location);

            if ($locationRef->location_type === 'kelas' && blank($request->room_number)) {
                throw ValidationException::withMessages([
                    'room_number' => 'Nomor ruang kelas wajib diisi.',
                ]);
            }

            $location = $locationRef->location_name;
            if ($locationRef->location_type === 'kelas' && filled($request->room_number)) {
                $location .= ' ' . trim($request->room_number);
            }

            $categoryId = (int) $request->id_category;
            $roomNumber = $locationRef->location_type === 'kelas' ? trim((string) $request->room_number) : null;
        } else {
            // In custom mode, use the provided id_category if it exists, otherwise fallback to first category
            $categoryId = $request->id_category;
            
            if (!$categoryId) {
                $defaultCategory = Category::query()->orderBy('id_category')->first();
                $categoryId = $defaultCategory ? $defaultCategory->id_category : null;
            }

            $defaultLocation = Location::query()
                ->where('is_active', true)
                ->where('location_type', 'lainnya')
                ->first() ?? Location::query()->where('is_active', true)->first();

            if (!$categoryId || !$defaultLocation) {
                throw ValidationException::withMessages([
                    'submission_mode' => 'Kategori/lokasi default belum tersedia. Hubungi admin.',
                ]);
            }

            $location = trim((string) $request->custom_location);
            $locationRef = $defaultLocation;
        }

        // 3. HANDLE IMAGE (Bener)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('aspirasi', 'public');
        }

        // 4. SIMPAN DATA (Bener)
        $createdInput = InputAspirations::create([
            'input_by' => Auth::guard('student')->id(),
            'submission_mode' => $request->submission_mode,
            'title' => trim((string) $request->title),
            'id_category' => $categoryId,
            'id_location' => $locationRef->id_location,
            'room_number' => $roomNumber,
            'location' => $location,
            'description' => trim((string) $request->description),
            'input_at' => now(),
            'submission_status' => 'menunggu',
            'image' => $imagePath,
        ]);

        try {
            $admins = User::role('admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewAspirationForAdminNotification($createdInput->load('student')));
            }
        } catch (\Exception $e) {
            \Log::error('Notification failed: ' . $e->getMessage());
        }

        // Redirect tanpa flash (SweetAlert handled di component onSuccess)
        return redirect()->route('student.input-aspirations.index');
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500',
        ]);

        $aspiration = InputAspirations::where('input_by', Auth::guard('student')->id())
            ->findOrFail($id);

        // Check if aspirasi sudah selesai
        if ($aspiration->submission_status !== 'diterima' || !$aspiration->aspiration || $aspiration->aspiration->progress_status !== 'Selesai') {
            return back()->with('error', 'Hanya aspirasi yang sudah selesai dapat diberi rating.');
        }

        $aspiration->update([
            'rating' => $request->rating,
            'feedback' => $request->feedback,
        ]);

        return back()->with('success', 'Terima kasih atas rating dan feedback mu!');
    }



    public function show($id)
    {
        $selectedAspiration = InputAspirations::with([
                'category',
                'aspiration.feedbacks.user.roles',
                'aspiration.comments',
                'aspiration.validator',
            ])
            ->where('input_by', Auth::guard('student')->id())
            ->findOrFail($id);

        return Inertia::render('Student/InputAspirations/Show', [
            'selectedAspiration' => $selectedAspiration,
            'aspirations' => InputAspirations::with(['category', 'aspiration'])
                ->where('input_by', Auth::guard('student')->id())
                ->latest()
                ->get(),
            'categories' => Category::all(),
            'locations' => Location::where('is_active', true)
                ->orderBy('location_name')
                ->get(['id_location', 'location_name', 'location_type']),
        ]);
    }


    public function destroy(InputAspirations $inputAspiration)
    {
        $aspiration = InputAspirations::where('input_by', Auth::guard('student')->id())
            ->findOrFail($inputAspiration->id_input);

        if ($aspiration->submission_status !== 'menunggu') {
            return redirect()->back()->with('error', 'Hanya aspirasi dengan status menunggu yang dapat dihapus.');
        }

        // Hapus file dari storage biar gak jadi bangkai
        if ($aspiration->image) {
            Storage::disk('public')->delete($aspiration->image);
        }

        $aspiration->delete();

        return redirect()->route('student.input-aspirations.index')->with('success', 'Aspirasi berhasil dihapus');
    }
}
