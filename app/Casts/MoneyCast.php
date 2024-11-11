<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MoneyCast implements CastsAttributes
{
    /**
     * Transform the integer stored in the database into a float.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return float
     */
    public function get($model, string $key, $value, array $attributes): float
    {
        return round(floatval($value) / 100, 2);
    }

    /**
     * Transform the float into an integer for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return float
     */
    public function set($model, string $key, $value, array $attributes): float
    {
        return round(floatval($value) * 100);
    }
}
