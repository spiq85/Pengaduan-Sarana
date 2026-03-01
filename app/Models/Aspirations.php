<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspirations extends Model
{
    protected $table = 'aspirations';

    protected $primaryKey = 'id_aspiration';

    protected $fillable = [
        'id_input',
        'input_by',
        'id_category',
        'description',
        'location',
        'validated_by',
        'validated_at',
        'progress_status',
        'priority_level',
        'start_at',
        'end_at'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'id_aspiration';
    }

    public function input()
    {
        return $this->belongsTo(InputAspirations::class, 'id_input', 'id_input');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'input_by', 'id_student');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by', 'id_user');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'id_aspiration', 'id_aspiration');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'aspiration_id', 'id_aspiration');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'aspiration_id', 'id_aspiration');
    }
}
