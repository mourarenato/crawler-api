<?php

namespace Database\Factories;

use App\Models\Iso4217;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class Iso4217Factory extends Factory
{
    protected $model = Iso4217::class;

    public function definition()
    {
        return [
            'code' => 'BRL',
            'number' => 986,
            'decimal' => 2,
            'currency' => 'Real',
            'currency_locations' => 'Brasil'
        ];
    }

}