<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id_student');
    }

    public function aspiration()
    {
        return $this->belongsTo(Aspirations::class, 'aspiration_id', 'id_aspiration');
    }
}
