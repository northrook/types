<?php

/**
 * Parent class will always be instanceof {@see Properties},
 * which also provides {@see Properties::validatePropertyName}.
 *
 * @noinspection PhpUndefinedClassInspection
 */

declare( strict_types = 1 );

namespace Northrook\Types\Traits;

use Northrook\Types\Type\Properties;

/**
 * Allows {@see Properties} to set readonly properties.
 *
 * @used-by  Properties
 *
 * @author   Martin Nielsen <mn@northrook.com>
 *
 * @link     https://github.com/northrook Properties Type Documentation
 * @todo     Update URL to documentation
 *
 *
 * @method static validatePropertyName( int|string $key )
 */
trait ReadonlyPropertiesTrait
{
    final public static function set( ...$properties ) : self {

        if ( parent::class !== Properties::class ) {
            trigger_error(
                'Class ' . parent::class . ' must be an instance of ' . Properties::class,
            );
        }

        $set = new static();

        foreach ( $properties as $key => $value ) {
            $key       = static::validatePropertyName( $key );
            $set->$key = $value;
        }

        return $set;
    }
}