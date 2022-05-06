<?php

namespace App\Services;

use App\Models\App;

class PasswordService
{
    const ALGORITHM = 'sha3-512';

    public static function hash($password): string
    {
        return hash(self::ALGORITHM, $password.env('PASSWORD_HASH_SALT'));
    }

    public static function check($password): bool
    {
        return App::get()->admin_password === self::hash($password);
    }
}