<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'id_category';

    protected $fillable = [
        'category_name',
        'description',
    ];

    public function inputAspirations()
    {
        return $this->hasMany(InputAspirations::class, 'id_category', 'id_category');
    }

    public function aspirations()
    {
        return $this->hasMany(Aspirations::class, 'id_category', 'id_category');
    }
}
