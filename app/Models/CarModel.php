<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    protected $fillable = ['desc', 'alias'];


    /**
     * Get the makes.
     */
    public function makes()
    {
        return $this->belongsTo(CarMake::class);
    }


    /**
     * Get the versions.
     */
    public function versions()
    {
        return $this->hasMany(CarVersion::class, 'car_models_id', 'id');
    }
}
