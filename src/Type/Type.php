<?php

namespace Northrook\Types\Type;


use Northrook\Types\Traits\PropertyAccessTrait;

/**
 * * Assign new type with `static::type( ... )`
 *
 * @property string $value
 */
abstract class Type
{
    use PropertyAccessTrait;

    public const TYPE = null;

    public readonly string $type;
    protected mixed        $value;

    public function __construct() {
        if ( false === isset( $this->value ) ) {
            echo 'Value not set';
        }
        $this->assignType();
    }

    /**
     * @return string
     */
    protected function getValue() : string {
        return $this->value;
    }

    protected function assignVariables( array $vars ) : void {
        foreach ( $vars as $property => $assign ) {
            if ( property_exists( $this, $property ) ) {
                $this->{$property} = $assign;
            }
        }
    }

    private function assignType() : void {
        $this->type = strtolower( $this::TYPE ?? gettype( $this->value ) );
    }

    /**
     * @param ...$set
     *
     * @return static
     */
    public static function type( ...$set ) : self {
        $set = new static( ... $set );

        $set->assignType();

        return $set;
    }
}