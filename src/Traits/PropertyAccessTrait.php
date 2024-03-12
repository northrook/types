<?php

declare( strict_types = 1 );

namespace Northrook\Types\Traits;

/**
 * @used-by \Northrook\Types\Type\Type
 */
trait PropertyAccessTrait
{
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

        if ( isset( $this->$name ) ) {
            return $this->$name;
        }

        if ( method_exists( $this, $name ) ) {
            return $this->$name();
        }

        trigger_error(
            "Attempted to access private or unknown property $name on " . static::class . ". Null returned instead.<br>",
            E_USER_WARNING,
        );

        return null;
    }

}