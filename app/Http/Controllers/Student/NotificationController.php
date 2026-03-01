<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read($id)
    {
        $notification = Auth::guard('student')->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return redirect()->route('student.input-aspirations.index');
    }
}
