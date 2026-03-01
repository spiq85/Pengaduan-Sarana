<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InputAspirations;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Carbon\Carbon;

class InputAspirationController extends Controller
{
    public function index()
    {
        $aspirations = InputAspirations::with(['category', 'aspiration'])
            ->where('input_by', Auth::guard('student')->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Student/InputAspirations/Index', [
            'aspirations' => $aspirations,
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        return Inertia::render('Student/InputAspirations/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        // 1. LOGIC COOLDOWN (5 Menit)
        $lastAspiration = InputAspirations::where('input_by', Auth::guard('student')->id())
            ->latest()
            ->first();

        if ($lastAspiration && $lastAspiration->created_at->addMinutes(5)->isFuture()) {
            $diff = now()->diff($lastAspiration->created_at->addMinutes(5));
            $menit = $diff->i;
            $detik = $diff->s;

            // Throw ValidationException → returns 422 → Inertia triggers onError
            throw ValidationException::withMessages([
                'cooldown' => "Sabar cuy! Tunggu $menit menit $detik detik lagi biar gak spam."
            ]);
        }

        // 2. VALIDASI (Bener)
        $request->validate([
            'id_category' => 'required|exists:categories,id_category',
            'location' => 'required|string|max:100',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // 3. HANDLE IMAGE (Bener)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('aspirasi', 'public');
        }

        // 4. SIMPAN DATA (Bener)
        InputAspirations::create([
            'input_by' => Auth::guard('student')->id(),
            'id_category' => $request->id_category,
            'location' => $request->location,
            'description' => $request->description,
            'input_at' => now(),
            'submission_status' => 'menunggu',
            'image' => $imagePath,
        ]);

        // Redirect tanpa flash (SweetAlert handled di component onSuccess)
        return redirect()->route('student.input-aspirations.index');
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        $aspiration = InputAspirations::where('input_by', Auth::guard('student')->id())
            ->findOrFail($id);

        $aspiration->update([
            'rating' => $request->rating,
            'feedback' => $request->feedback,
        ]);

        return back()->with('message', 'Rating berhasil dikirim!');
    }

    public function show($id)
    {
        // Cari data milik siswa yang sedang login
        $aspiration = InputAspirations::with([
                'category',
                'aspiration.validator',
                'aspiration.feedbacks.user.roles',
            ])
            ->where('input_by', Auth::guard('student')->id())
            ->findOrFail($id);

        return Inertia::render('Student/InputAspirations/Show', [
            'aspiration' => $aspiration
        ]);
    }

    public function edit($id)
    {
        // Cari data pake ID manual karena PK lu custom (id_input)
        $aspiration = InputAspirations::where('input_by', Auth::guard('student')->id())
            ->findOrFail($id);

        if (in_array($aspiration->submission_status, ['diterima', 'ditolak'])) {
            return redirect()->back()->with('error', 'Data tidak dapat diubah.');
        }

        return Inertia::render('Student/InputAspirations/Edit', [
            'aspiration' => $aspiration,
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, $id)
    {
        $aspiration = InputAspirations::where('input_by', Auth::guard('student')->id())
            ->findOrFail($id);

        if ($aspiration->submission_status !== 'menunggu') {
            return back()->with('error', 'Hanya aspirasi dengan status menunggu yang dapat diubah.');
        }

        $request->validate([
            'id_category' => 'required|exists:categories,id_category',
            'location' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        $aspiration->update([
            'id_category' => $request->id_category,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        return redirect()->route('student.input-aspirations.index')->with('success', 'Berhasil update!');
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
