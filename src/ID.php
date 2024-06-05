<?php

declare( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Types\Interfaces\Printable;
use Northrook\Types\Traits\PrintableTypeTrait;
use Northrook\Types\Type\Type;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

class ID extends Type implements Printable
{
    use PrintableTypeTrait;

    public function __construct(
        ?string     $id = null,
        bool | Uuid $generate = false,
    ) {
        trigger_deprecation(
            $this::class,
            '1.0.0',
            $this::class . ' is deprecated, use Northrook\Type instead',
        );

        if ( $id && !$generate ) {
            $this->value = $this->validateIdString( $id );
        }

        if ( true === $generate ) {
            $id = new Ulid();
        }
        if ( $generate instanceof Uuid ) {
            $id = $generate->toRfc4122();
        }

        $this->value = $id;

        parent::__construct();
    }

    private function validateIdString( string $string ) : string {

        if ( preg_match( '/[^A-Za-z0-9_-]/', $string ) ) {
            trigger_error(
                "Invalid property name: \"$string\".<br>
                 Property names may only contain alphanumeric characters and underscores.<br>",
                E_USER_ERROR,
            );
        }

        // if ( str_contains( $string, '-' ) ) {
        //     trigger_error(
        //         "Property name \"$string\" cannot contain hyphens.<br>Hyphens have been converted to underscores.<br>",
        //     );
        //     $string = str_replace( '-', '_', $string );
        // }

        return $string;
    }

}