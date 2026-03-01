<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $table = 'students';

    protected $primaryKey = 'id_student';

    protected $fillable = [
        'nis',
        'username',
        'password',
        'class',
    ];

    protected $hidden = [
        'password',
    ];

    public function inputAspirations() 
    {
        return $this->hasMany(InputAspirations::class, 'input_by', 'id_student'); 
    }

    public function aspirations()
    {
        return $this->hasMany(Aspirations::class, 'input_by', 'id_student');
    }
}
