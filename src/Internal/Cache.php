<?php

declare( strict_types = 1 );

namespace Northrook\Types\Internal;

use JetBrains\PhpStorm\ExpectedValues;
use Northrook\Logger\Log;
use ReflectionClass;
use ReflectionException;

final class Cache
{

    /**
     * @var ReflectionClass[]
     */
    private static array $reflectionCache = [];

    private function __construct() {}

    private function __clone() {}

    public static function getReflectionClass( string $class ) : ?ReflectionClass {
        if ( !isset( self::$reflectionCache[ $class ] ) ) {
            try {
                self::$reflectionCache[ $class ] = new ReflectionClass( $class );
            }
            catch ( ReflectionException $e ) {
                Log::Error(
                    'Could not get reflection class for {class}, returned {return}. ',
                    [ 'class' => $class, 'return' => 'null', 'exception' => $e, ],
                );
                return null;
            }
        }

        return self::$reflectionCache[ $class ];
    }

    public static function clear(
        #[ExpectedValues( [ 'all', 'reflection' ] )]
        string $pool,
    ) : void {
        match ( $pool ) {
            'reflection' => self::$reflectionCache = [],
            'all'        => function () : void {
                self::$reflectionCache = [];
            },
        };
    }

    public static function getReflectionClasses() : array {
        return self::$reflectionCache;
    }

}