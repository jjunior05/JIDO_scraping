<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarVersionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'width',
        'height',
        'traction',
        'fuel',
        'cc',
        'doors',
        'air_bag',
        'abs',
        'steering_wheel',
        'air_cond',
        'bluetooth',
        'tela',
        'android',
        'tires',
    ];

}
