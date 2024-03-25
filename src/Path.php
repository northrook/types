<?php

declare ( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Types\Interfaces\Printable;
use Northrook\Types\Traits\PrintableTypeTrait;
use Northrook\Types\Traits\ValueHistoryTrait;
use Northrook\Types\Type\Validated;
use Stringable;

/**
 * @property string  $filename
 * @property ?string $extension
 * @property bool    $exists
 * @property bool    $isDir
 */
class Path extends Validated implements Printable, Stringable
{
    use PrintableTypeTrait;
    use ValueHistoryTrait;

    private array $pathinfo;

    public function __construct( string $value ) {
        $this->updateValue( $value );
        parent::__construct();
    }

    protected function filename() : string {
        return $this->pathinfo( 'filename' );
    }

    /**
     * Get the extension of the {@see Path::$value}.
     *
     * * Returns 'dir' if the {@see Path::$value} is a directory.
     *
     * @return null|string
     */
    protected function extension() : ?string {
        return $this->pathinfo( 'extension' ) ?: ( $this->isDir ? 'dir' : null );
    }

    /**
     * Check if the {@see Path::$value} is a directory.
     *
     * @return bool
     */
    protected function isDir() : bool {
        return is_dir( $this->value );
    }

    /**
     * Check if the {@see Path::$value} exists.
     *
     * * Run whenever the {@see Path::$value} is accessed.
     *
     * @return bool
     */
    protected function exists() : bool {
        return file_exists( $this->value );
    }

    /**
     * Validate the current {@see Path::$value}.
     *
     * * Normalizes the {@see $value}.
     * * Checks if it {@see exists()}.
     * * Stored in {@see $history} if valid.
     * * Sets the {@see $extension}.
     *
     * @return bool
     */
    protected function validate() : bool {
        $this->value = static::normalize( $this->value );

        return $this->exists();
    }

    /**
     * Set a new {@see Path::$value}.
     *
     * * Replaces the current {@see $value}.
     * * Stored in {@see $history} if valid.
     *
     * @param string  $string
     *
     * @return $this
     */
    public function set( string $string ) : Path {
        $this->updateValue( $string );

        return $this;
    }


    /**
     * Prepend a string to the current {@see Path::$value}.
     *
     * * `/this/is/example/path/` . {@see $string}.
     * * Stored in {@see $history} if valid.
     *
     * @param string  $string
     *
     * @return $this
     */
    public function add( string $string ) : Path {
        $this->updateValue( $this->value . $string );

        return $this;
    }

    /**
     * Normalise a `string`, assuming it is a `path`.
     *
     * * Removes repeated slashes.
     * * Normalises slashes to system separator.
     * * Prevents backtracking.
     * * Optional trailing slash for directories.
     * * No validation is performed.
     *
     * @param string  $string
     * @param bool    $trailingSlash
     *
     * @return string
     */
    public static function normalize( string $string, bool $trailingSlash = true ) : string {

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

        return $trailingSlash ? $path . DIRECTORY_SEPARATOR : $path;
    }

    protected function pathinfo( ?string $get = null ) : null | string | array {
        $this->pathinfo ??= pathinfo( $this->value );

        if ( $get ) {
            return $this->pathinfo[ $get ] ?? null;
        }

        return $this->pathinfo;
    }

}