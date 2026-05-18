<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    protected $fillable = [
        'id_student',
        'message',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'id_student', 'id_student');
    }
}
