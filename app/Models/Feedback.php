<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $primaryKey = 'id_feedback';

    protected $fillable = [
        'id_aspiration',
        'feedback_by',
        'message',
        'feedback_at',
    ];

    protected $casts = [
        'feedback_at' => 'date',
    ];

    public function aspiration()
    {
        return $this->belongsTo(Aspirations::class, 'id_aspiration', 'id_aspiration');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'feedback_by', 'id_user');
    }
}
