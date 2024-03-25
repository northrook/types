<?php

declare( strict_types = 1 );

namespace Northrook\Types\Internal;

trait SupportFunctionsTrait
{
    protected static function validateKeyString( string $string, ) : string {

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
}