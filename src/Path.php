<?php

declare ( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Types\Interfaces\Printable;
use Northrook\Types\Traits\PrintableTypeTrait;
use Northrook\Types\Traits\ValueHistoryTrait;
use Northrook\Types\Type\Validated;
use Stringable;

/**
 * @property ?string $extension
 */
class Path extends Validated implements Printable, Stringable
{
    use PrintableTypeTrait;
    use ValueHistoryTrait;

    public function __construct( string $value ) {
        $this->updateValue( $value );
        parent::__construct();
    }

    protected function extension() : ?string {
        return pathinfo( $this->value, PATHINFO_EXTENSION );
    }

    protected function validate() : bool {
        $this->value = static::normalize( $this->value );

        return file_exists( $this->value );
    }

    public function set( string $string ) : Path {
        $this->updateValue( $string );

        return $this;
    }

    public function add( string $string ) : Path {
        $this->updateValue( $this->value . $string );

        return $this;
    }

    /**
     * @param string  $string
     *
     * @return string
     */
    public static function normalize( string $string ) : string {

        $string = mb_strtolower( strtr( $string, "\\", "/" ) );

        if ( str_contains( $string, '/' ) ) {

            $path = [];

            foreach ( array_filter( explode( '/', $string ) ) as $part ) {
                if ( $part === '..' && $path && end( $path ) !== '..' ) {
                    array_pop( $path );
                }
                else {
                    if ( $part !== '.' ) {
                        $path[] = trim( $part );
                    }
                }
            }

            $path = implode(
                separator : DIRECTORY_SEPARATOR,
                array     : $path,
            );
        }
        else {
            $path = $string;
        }

        // If the string contains a valid extension, return it as-is
        if ( isset( pathinfo( $path )[ 'extension' ] ) && !str_contains( pathinfo( $path )[ 'extension' ], '%' ) ) {
            return $path;
        }

        return $path . DIRECTORY_SEPARATOR;
    }

}