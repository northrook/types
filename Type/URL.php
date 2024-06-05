<?php /** @noinspection DuplicatedCode */

namespace Northrook\Type;

use Northrook\Core\Trait\PropertyAccessor;
use Northrook\Logger\Timer;
use Northrook\Support\Str\PathFunctions;
use Northrook\Type\Interface\PathType;
use Northrook\Type\Internal\Type;
use Northrook\Type\Internal\ValueMemoizationCache;

/**
 * @property string $value
 * @property string $scheme
 * @property string $filename
 * @property string $hostname
 * @property string $path
 * @property bool   $exists
 * @property bool   $isValid
 * @property bool   $isExternal
 */
final class URL extends Type implements PathType
{
    use PropertyAccessor, ValueMemoizationCache, PathFunctions;

    private readonly array         $parts;
    private readonly array | false $headers;
    private ?bool                  $isExternal = null;

    protected string $value;
    protected bool   $isValid;

    /**
     * Strict:
     * - Will check if the path exists whenever the value is accessed.
     * - A {@see FileNotFoundException} will be thrown if the path does not exist.
     *
     * @param string|URL  $value   Passing a {@see URL} will extract its value.
     * @param bool        $strict  Validate the path exists.
     */
    public function __construct(
        string | URL             $value,
        private readonly ?string $match_scheme = 'https',
        private readonly ?string $match_hostname = null,
        private readonly bool    $strict = false,
        bool                     $useCache = true,

    ) {
        $this->value = $this->value( $value );
    }

    public function __get( string $property ) {
        return match ( $property ) {
            'value'      => $this->value,
            'exists'     => $this->getUrlStatus(),
            'isValid'    => $this->validate(),
            'isExternal' => $this->isUrlExternal(),
            'scheme'     => $this->getUrlParts( 'scheme' ),
            'hostname'   => $this->getUrlParts( 'host' ),
            'filename'   => $this->getUrlParts( 'filename' ),
            'path'       => $this->getUrlParts( 'path' ),
            default      => null,
        };
    }

    public function __toString() : string {
        $this->validate();
        return $this->value;
    }

    public function validate() : bool {

        if ( isset( $this->isValid ) ) {
            return $this->isValid;
        }

        // Validate defined scheme
        if ( $this->match_scheme && !str_starts_with( $this->value, $this->match_scheme . '://' ) ) {
            return false;
        }

        // A URL must contain a scheme, and a host somewhere
        if ( !( str_contains( $this->value, "//" ) && str_contains( $this->value, '.' ) ) ) {
            return false;
        }

        $url = filter_var( $this->value, FILTER_VALIDATE_URL );

        if ( $url === false ) {
            return $this->isValid = false;
        }

        $this->value = $url;

        $this->isUrlExternal();

        return $this->isValid = $this->getUrlStatus();
    }

    private function getUrlStatus() : bool {

        Timer::start( 'header_fetch' );

        $this->headers ??= get_headers( $this->value, true );

        if ( false === $this->headers ) {
            return false;
        }

        if ( str_contains( $this->headers[ 0 ], '200' ) ) {
            $this->getUrlParts();
            return true;
        }

        return false;
    }
    
    private function getUrlParts( ?string $get = null ) : null | string | array {

        Timer::start( 'url_fetch' );

        if ( !isset( $this->parts ) ) {

            $url = parse_url( $this->value );

            if ( isset( $url[ 'path' ] ) ) {
                $url[ 'filename' ] = trim( strrchr( $url[ 'path' ], '/' ), " \n\r\t\v\0/" );
            }

            if ( isset( $url[ 'path' ] ) && str_contains( $url[ 'path' ], '@' ) ) {
                $url[ 'version' ] = strstr(
                    substr(
                        $url[ 'path' ],
                        strrpos( $url[ 'path' ], '@' ),
                    ), '/', true,
                );
            }

            $this->parts = $url;

        }

        if ( $get ) {
            return $this->parts[ $get ] ?? null;
        }

        return $this->parts;
    }

    private function isUrlExternal() : ?bool {

        if ( is_bool( $this->isExternal ) ) {
            return $this->isExternal;
        }

        if ( null === $this->match_hostname ) {
            return null;
        }

        $host = parse_url( $this->match_hostname, PHP_URL_HOST );
        $url  = parse_url( $this->value, PHP_URL_PATH );

        if ( !$host || !$url ) {
            return null;
        }

        if ( !isset( $host[ 'host' ], $url[ 'host' ] ) ) {
            return null;
        }

        return $this->isExternal = $host[ 'host' ] === $url[ 'host' ];
    }
}