<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputAspirations extends Model
{
    protected $table = 'input_aspirations';

    protected $primaryKey = 'id_input';

    protected $fillable = [
        'input_by',
        'input_at',
        'id_category',
        'submission_status',
        'description',
        'location',
        'image',
        'admin_message',
        'rating',
        'feedback',
    ];

    protected $casts = [
        'input_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'input_by', 'id_student');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function aspiration()
    {
        return $this->hasOne(Aspirations::class, 'id_input', 'id_input');
    }

    public function progress()
    {
        return $this->hasMany(Aspirations::class, 'id_input', 'id_input')->orderBy('created_at', 'desc');
    }

    // Scope for filtering aspirations
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        })->when($filters['category'] ?? null, function ($query, $category) {
            $query->where('id_category', $category);
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('submission_status', $status);
        })->when($filters['progress'] ?? null, function ($query, $progress) {
            // FILTER BARU: Nembus ke tabel aspirations
            $query->whereHas('aspiration', function ($q) use ($progress) {
                $q->where('progress_status', $progress);
            });
        });
    }
}
