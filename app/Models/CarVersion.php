<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarVersion extends Model
{
    use HasFactory;


    protected $fillable = ['desc'];


    public function versions()
    {
        return $this->hasMany(CarVersionItem::class, 'car_version_id', 'id');
    }
}
