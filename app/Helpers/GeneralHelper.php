<?php

namespace App\Helpers;

class GeneralHelper
{
    public static function camelToSnakeCase(string $string): string
    {
        return static::splitStringOnUppercase($string, '_', 'strtolower');
    }

    public static function getInitialsOnUpperCase(string $string): string
    {
        $initals = '';

        for ($i = 0, $n = strlen($string); $i < $n; $i++) {
            if (ctype_upper($string[$i])) {
                $initals .= $string[$i];
            }
        }

        return $initals;
    }

    public static function pascalToSnakeCase(string $string): string
    {
        return static::camelToSnakeCase($string);
    }

    public static function snakeToCamelCase(string $string): string
    {
        return lcfirst(static::snakeToPascalCase($string));
    }

    public static function snakeToPascalCase(string $string): string
    {
        return implode('', array_map('ucfirst', explode('_', $string)));
    }

    public static function splitStringOnUppercase(string $string, string $delimiter, string $castTo = null): string
    {
        $parts = [];

        for ($i = 0, $n = strlen($string), $j = 0; $i < $n; $i++) {
            if ($i !== 0 && ctype_upper($string[$i])) {
                $j++;
            }

            isset($parts[$j]) ? $parts[$j] .= $string[$i] : $parts[$j] = $string[$i];
        }

        return implode($delimiter, $castTo ? array_map($castTo, $parts) : $parts); 
    }
}
