<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

Broadcast::channel('student.{id}', function ($user, $id) {
    // Kita paksa cek pake guard student
    $student = Auth::guard('student')->user();

    if (!$student) return false;

    return (int) $student->id_student === (int) $id;
}, ['guard' => 'student', 'provider' => 'student']);
