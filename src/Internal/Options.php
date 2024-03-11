<?php

namespace Northrook\Types\Internal;

final class Options
{
    private static array $options = [
        'password' => [
            'minimumStrength' => 4,
        ],
    ];

    private function __construct() {}

    private function __clone() {}

    public static function set( string $option, string | int | bool $value ) : void {
        self::$options[ $option ] = $value;
    }

    public static function get( string $option ) : mixed {
        return self::$options[ $option ] ?? null;
    }


}