<?php

declare( strict_types = 1 );

namespace Northrook\Types\Type;

use Stringable;

/**
 * @author  Martin Nielsen <mn@northrook.com>
 *
 * @link    https://github.com/northrook Documentation
 * @todo    Update URL to documentation
 *
 * @method getName() =
 */
abstract class Enum implements Stringable
{

    protected const CASES = [];

    /**
     * @var Enum[] [ self::class => Enum ]
     */
    protected static array $inventory = [];

    /**
     * @param string  $name   The name for this Enum
     * @param mixed   $value  The backing value for this Enum
     */
    final private function __construct(
        public readonly string $name,
        public readonly mixed  $value,
    ) {}

    /**
     * Get an Enum instance from a name or value
     *
     * @template N of key-of<static::getNames>
     *
     * @param N  $name
     *
     */
    final public static function from(
        string $name = null,
        mixed  $value = null,
    ) : static {
        return static::get( $name, $value );
    }

//    final public static tryFrom(){
//Log::Warning(
//"The Enum {className} has no names defined.",
//[
//'className' => static::class,
//'value'     => $value,
//'cases'     => static::CASES,
//],
//);}

    final public static function get(
        string $name = null,
        mixed  $value = null,
    ) : static {

        // Ensure that cases have been defined
        if ( empty( static::CASES ) ) {
            trigger_error(
                "The Enum " . static::class . " has no names defined.",
                E_USER_ERROR,
            );
        }

        // Non-backed enums
        if ( array_is_list( static::CASES ) ) {
            $name ??= $value;
            if ( !in_array( $name, static::CASES ) ) {
                throw new ValueError(
                    'Uncaught ValueError: "' . $enum . '" is not a valid backing value for enum ' . static::class,
                );
            }
            $value = $name;
        }
        // Backed enums
        else {
            if ( $name && !array_key_exists( $name, static::CASES ) ) {
                throw new ValueError(
                    'Uncaught NameError: "' . $name . '" is not a valid name for enum ' . static::class,
                );
            }
            if ( $value && !in_array( $value, static::CASES ) ) {
                throw new ValueError(
                    'Uncaught ValueError: "' . $value . '" is not a valid backing value for enum ' . static::class,
                );
            }
            $name  ??= array_search( $value, static::CASES );
            $value = static::CASES[ $name ];
        }

        $key = static::class . ':' . gettype( $value ) . '$' . $name;

        if ( isset( static::$inventory[ $key ] ) ) {
            return static::$inventory[ $key ];
        }

        return static::$inventory[ $key ] = new static( $name, $value );


    }

//    private static function caseExists( mixed $case ) : bool {
////        if ( empty( static::CASES ) ) {
////            trigger_error(
////                "The Enum " . static::class . " has no names defined.",
////                E_USER_ERROR,
////            );
////        }
//        if ( array_is_list( static::CASES ) ) {
//            return in_array( $case, static::CASES );
//        }
//        return array_key_exists( $case, static::CASES );
//    }

    /**
     * Retrieve al the registered Enum instances
     *
     * @return Enum[]
     */
    public static function getInventory() : array {
        return static::$inventory;
    }

    /**
     * Returns a string with the Enum case name.
     *
     * @return string
     */
    public function __toString() : string {
        return $this->name;
    }

    /**
     * Retrieve all cases of this Enum
     *
     * @return array
     */
    public static function cases() : array {
        return static::CASES;
    }

    /**
     * @return array{'name': string, "value": mixed}
     */
    public static function getNames() : array {
        return array_is_list( static::CASES ) ? static::CASES : array_keys( static::CASES );
    }
}
