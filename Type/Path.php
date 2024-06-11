<?php

namespace Northrook\Type;

use Northrook\Core\Trait\PropertyAccessor;
use Northrook\Support\File;
use Northrook\Support\Str\PathFunctions;
use Northrook\Type\Interface\PathType;
use Northrook\Type\Internal\Type;
use Northrook\Type\Internal\ValueMemoizationCache;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * @property-read  string $value
 * @property-read  bool   $isValid
 * @property-read  string $basename
 * @property-read  string $filename
 * @property-read  string $extension
 * @property-read  bool   $exists
 * @property-read  bool   $isDir
 * @property-read  bool   $isFile
 * @property-read  bool   $isUrl
 * @property-read  bool   $isWritable
 * @property-read  int    $lastModified
 * @property-read  string $mimeType
 */
final class Path extends Type implements PathType
{
    use PropertyAccessor, ValueMemoizationCache, PathFunctions;

    private array  $pathInfo;
    private string $mimeType;

    protected string $value;
    protected bool   $isValid;

    /**
     * Strict:
     * - Will check if the path exists whenever the value is accessed.
     * - A {@see FileNotFoundException} will be thrown if the path does not exist.
     *
     * @param string|Path  $value   Passing a {@see Path} will extract its value.
     * @param bool         $strict  Validate the path exists.
     */
    public function __construct(
        string | Path         $value,
        private readonly bool $strict = false,
        bool                  $useCache = true,

    ) {
        $this->value = $this->value( $value );
    }

    public function __get( string $property ) : bool | string {
        return match ( $property ) {
            'value'        => $this->value,
            'isValid'      => $this->isValid ?? $this->validate(), // only check if not already validated
            'basename'     => pathinfo( $this->value, PATHINFO_BASENAME ),
            'filename'     => pathinfo( $this->value, PATHINFO_FILENAME ),
            'extension'    => pathinfo( $this->value, PATHINFO_EXTENSION ),
            'exists'       => file_exists( $this->value ),
            'isDir'        => is_dir( $this->value ),
            'isFile'       => is_file( $this->value ),
            'isWritable'   => is_writable( $this->value ),
            'isReadable'   => is_readable( $this->value ),
            'lastModified' => filemtime( $this->value ),
            'mimeType'     => $this->mimeType ??= File::getMimeType( $this->value ),
            default        => null,
        };
    }

    public function __toString() : string {
        $this->validate();
        return $this->value;
    }

    public function append( string $string, bool $clone = false ) : Path {

        if ( $clone ) {
            return ( clone $this )->append( $string );
        }

        $this->value .= DIRECTORY_SEPARATOR . ltrim( $string, '/\\' );

        $this->validate();

        return $this;
    }

    public function validate() : bool {

        $this->value = $this::normalizePath( $this->value );

        $this->isValid = file_exists( $this->value );

        if ( $this->strict && !$this->isValid ) {
            throw new FileNotFoundException(
                message : 'The path does not exist. Please verify the filename and try again.',
                path    : $this->value,
            );
        }

        return $this->isValid;
    }

}