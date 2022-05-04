<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function new(array $attributes = []): self
    {
        $model = new static;

        foreach ($attributes AS $key => $value)  {
            $model->{$key} = $value;
        }

        return $model;
    }
}
