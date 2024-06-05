<?php

namespace Northrook\Type;

use JetBrains\PhpStorm\ExpectedValues;
use Northrook\Type\Interface\IdType;
use Northrook\Type\Internal\Type;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

class ID extends Type implements IdType
{


    private const DEFAULT_ALGORITHM = 'ulid';

    private const ALGORITHMS = [
        'ulid',
        'uuid::v7', // v7
        'xxh128',
    ];

    private const HASH_TYPES = [
        'xxh128'   => 'deterministic',
        'ulid'     => 'identifier',
        'uuid::v7' => 'identifier',
    ];

    public readonly string         $value;
    public readonly false | string $hash;      // false | 'deterministic' | 'identifier'
    public readonly ?string        $algorithm; // null | 'ulid' | 'uuid::v7' | 'xxh128'

    /**
     * - Passing an {@see ID} will extract its value.
     * - Passing a string will validate it and convert it to an {@see ID}.
     * - Passing null will create a new {@see ID}.
     *
     * @param null|string|ID  $value  ID value
     * @param null|string     $hash   Hash algorithm to use
     */
    public function __construct(
        #[\SensitiveParameter]
        null | string | ID $value = null,
        #[ExpectedValues( self::ALGORITHMS )]
        ?string            $hash = null,
    ) {
        if ( !$value ) {
            $this->value = $this->hashValue( null, $hash ?? ID::DEFAULT_ALGORITHM );
            return;
        }

        if ( $hash ) {
            $this->value = $this->hashValue( $this->value( $value ), $hash );
            return;
        }

        $this->value( $value );

        $this->hash      = false;
        $this->algorithm = null;
    }

    public function __toString() : string {
        return $this->value;
    }

    public function __get( string $property ) : ?string {
        return match ( $property ) {
            'value' => $this->value,
            default => null,
        };
    }

    /**
     * Hash a value to a deterministic ID using the xxh128 algorithm.
     *
     * - Value will be stringified using `print_r()`.
     * - Value is then hashed using `hash()`.
     * - The value is uppercased.
     * - The hash is not reversible.
     *
     * @param string  $value
     *
     * @return ID
     */
    public static function hash( mixed $value ) : ID {
        return new self( $value, 'xxh128' );
    }

    /**
     * Generate a new ULID.
     *
     * - The ULID is generated using `Ulid::generate()`.
     * - The ID is sortable
     *
     * @return ID
     */
    public static function ulid() : ID {
        return new self( hash : 'ulid' );
    }

    /**
     * Generate a new UUID v7.
     *
     * - The UUID is generated using `Uuid::v7()`.
     * - The ID is sortable
     *
     * @return ID
     */
    public static function uuid() : ID {
        return new self( hash : 'uuid::v7' );
    }

    /**
     * Hash the provided `value`, or generate a hashed string.
     *
     * - Based on the provided `$algorithm`.
     * - Sets the {@see $algorithm} property.
     * - Sets the {@see $hash} property.
     *
     * @param ?string  $value
     * @param string   $algorithm
     *
     * @return string
     */
    private function hashValue( ?string $value, string $algorithm ) : string {
        $this->algorithm = $algorithm;
        $value           = ID::generate( $value, $algorithm );
        $this->hash      = ID::HASH_TYPES[ $algorithm ];

        return $value;
    }

    public static function generate( mixed $value = null, string $algorithm = 'ulid' ) : string {

        $value = $value instanceof Type ? $value->value : $value;

        return match ( $algorithm ) {
            'ulid'     => Ulid::generate(),
            'uuid::v7' => Uuid::v7(),
            'xxh128'   => strtoupper( hash( 'xxh128', print_r( $value, true ), false ) ),
            default    => throw new \InvalidArgumentException( "Unknown hash algorithm: {$algorithm}" ),
        };
    }
}