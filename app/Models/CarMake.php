<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarMake extends Model
{
    use HasFactory;

    protected $fillable = ['desc', 'alias'];


    /**
     * Get the categories.
     */
    public function categories()
    {
        return $this->belongsTo(CarCategories::class, 'car_categories_id', 'id');
    }

    /**
     * Get the versions.
     */
    public function models()
    {
        return $this->hasMany(CarModel::class, 'car_makes_id', 'id');
    }
}
