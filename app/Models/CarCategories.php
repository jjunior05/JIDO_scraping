<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCategories extends Model
{
    use HasFactory;

    protected $table = 'car_categories';


    /**
     * Get the makes.
     */
    public function makes()
    {
        return $this->hasMany(CarMake::class, 'car_categories_id', 'id');
    }
}
