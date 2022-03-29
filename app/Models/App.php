<?php

namespace App\Models;

class App extends BaseModel
{
    protected $table = 'app';

    public static function get(): self
    {
        return self::find(1);
    }
}
