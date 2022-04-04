<?php

namespace App\Helpers;

class GeneralHelper
{
    public static function camelToSnakeCase($string): string
    {
        $parts = [];

        for ($i = 0, $n = strlen($string), $j = 0; $i < $n; $i++) {
            if ($i !== 0 && ctype_upper($string[$i])) {
                $j++;
            }

            isset($parts[$j]) ? $parts[$j] .= $string[$i] : $parts[$j] = $string[$i];
        }

        return implode('_', array_map('strtolower', $parts));
    }

    public static function pascalToSnakeCase($string): string
    {
        return static::camelToSnakeCase($string);
    }

    public static function snakeToCamelCase($string): string
    {
        return lcfirst(static::snakeToPascalCase($string));
    }

    public static function snakeToPascalCase($string): string
    {
        return implode('', array_map('ucfirst', explode('_', $string)));
    }
}
