<?php

declare( strict_types = 1 );

namespace Northrook\Type\Internal;

/**
 * @property-read mixed $value
 *
 * @internal
 *
 * @author  Martin Nielsen <mn@northrook.com>
 *
 * @link    https://github.com/northrook/types
 */
abstract class Type
{

    /**
     * Return the value for this {@see Type}.
     *
     * - Use this in the {@see Type::__construct} method.
     * - Ensures the {@see Type::$value} property is set.
     * - Will extract the value is passed a {@see Type} instance.
     *
     * @param mixed  $value
     *
     * @return mixed
     */
    final protected function value( mixed $value ) : mixed {
        if ( !property_exists( $this, 'value' ) ) {
            throw new \LogicException( 'The ' . $this::class . ' class must have a value property.' );
        }
        return $value instanceof Type ? $value->value : $value;
    }

    final public function returnType() : string {
        return gettype( $this->value );
    }

}