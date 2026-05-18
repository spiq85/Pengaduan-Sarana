<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';
    protected $primaryKey = 'id_location';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'location_name', 'location_type', 'is_active'
    ];

    public function inputAspirations()
    {
        return $this->hasMany(InputAspirations::class, 'id_location', 'id_location');
    }
}
