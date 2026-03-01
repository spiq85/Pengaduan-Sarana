<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aspirations;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request, Aspirations $aspiration)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Feedback::create([
            'id_aspiration' => $aspiration->id_aspiration,
            'feedback_by'   => auth()->id(),
            'message'       => $request->message,
            'feedback_at'   => now()->toDateString(),
        ]);

        return redirect()->back()->with('success', 'Feedback berhasil ditambahkan');
    }
}
