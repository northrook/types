<?php

declare( strict_types = 1 );

namespace Northrook\Types\Type;

use Northrook\Logger\Log;
use Northrook\Logger\Status\HTTP;
use Northrook\Types\Exception\InvalidPropertyNameException;
use Northrook\Types\Internal\Cache;
use Northrook\Types\Traits\ReadonlyPropertiesTrait;
use ReflectionException;
use ReflectionProperty;
use stdClass;


/**
 * Properties and options.
 *
 * @author  Martin Nielsen <mn@northrook.com>
 *
 * @link    https://github.com/northrook Documentation
 * @todo    Update URL to documentation
 *
 */
abstract class Properties extends stdClass
{

    /**
     * Set properties by assigning `named: $value` pairs, or by passing and spreading an array of `[key=>value]` pairs.
     *
     * * Use `camelCase` or `snake_case` for keys.
     * * `snake_case` should be used when a `hyphenated-output` is expected,
     * * Values can be any type, as long as you handle their expected output.
     * * For readonly properties, use {@see ReadonlyPropertiesTrait} on the extending class.
     *
     * @param mixed  ...$properties
     *
     * @return Properties
     */
    public static function set( ...$properties ) : self {
        $set = new static();

        foreach ( $properties as $key => $value ) {
            if ( false === is_string( $key ) || empty( $key ) ) {
                Log::Alert(
                    'Invalid property name {name} in {propertiesClass}, property name must be a {type}. The property has not been set.',
                    [
                        'name'            => $key,
                        'propertiesClass' => static::class,
                        'type'            => 'string',
                        'exception'       => new InvalidPropertyNameException(
                            message : 'Property name must be a string.',
                            code    : HTTP::UNPROCESSABLE_ENTITY
                        ),
                    ],
                );
                continue;
            }
            $set->$key = $value;
        }

        return $set;
    }


    /**
     * Access public and protected properties.
     *
     * * Private properties are not accessible.
     *
     * @param string  $name
     *
     * @return mixed The value of the property, or null if it does not exist or is private
     */
    public function __get( string $name ) : mixed {

        if ( $this->has( $name ) ) {
            return $this->$name;
        }

        trigger_error(
            "Attempted to access private or unknown property $name on " . static::class . ". Null returned instead.<br>",
            E_USER_WARNING,
        );

        return null;
    }

    /**
     * Resets the properties to a new set.
     *
     * * Each existing property will be unset.
     * * Does not affect readonly properties.
     * * Each new property will be added.
     *
     * @param mixed  $new
     *
     * @return $this
     */
    public function reset( ...$new ) : self {

        // Loop through existing properties
        foreach ( $this as $currentKey => $currentValue ) {
            // Skip readonly properties, as they cannot be unset
            if ( $this->property( $currentKey )?->isReadOnly() ?? true ) {
                continue;
            }
            unset( $this->$currentKey );
        }

        // Assign new properties
        foreach ( $new as $newKey => $newValue ) {
            $this->$newKey = $newValue;
        }

        return $this;
    }

    public function add( string $key, mixed $value, bool $override = false ) : self {

        if ( !$override && $this->has( $key ) ) {
            return $this;
        }

        $this->$key = $value;

        return $this;
    }

    public function has( string $key ) : bool {
        return isset( $this->$key );
    }

    public function remove( string $key ) : self {
        if ( $this->has( $key ) ) {
            unset( $this->$key );
        }
        return $this;
    }

    protected static function validatePropertyName( string $string ) : string {

        if ( preg_match( '/[^A-Za-z0-9_-]/', $string ) ) {
            trigger_error(
                "Invalid property name: \"$string\".<br>
                 Property names may only contain alphanumeric characters and underscores.<br>",
                E_USER_ERROR,
            );
        }

        if ( str_contains( $string, '-' ) ) {
            trigger_error(
                "Property name \"$string\" cannot contain hyphens.<br>Hyphens have been converted to underscores.<br>",
            );
            $string = str_replace( '-', '_', $string );
        }

        return $string;
    }

    /**
     *
     *
     * @param string  $string
     *
     * @return null|ReflectionProperty
     */
    private function property( string $string ) : ?ReflectionProperty {
        try {
            return Cache::getReflectionClass( $this::class )->getProperty( $string );
        }
        catch ( ReflectionException ) {
            return null;
        }
    }

}