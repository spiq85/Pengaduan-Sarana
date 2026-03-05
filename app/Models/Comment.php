<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['aspiration_id', 'student_id', 'body'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id_student');
    }

    public function aspiration()
    {
        return $this->belongsTo(Aspirations::class, 'aspiration_id', 'id_aspiration');
    }
}
