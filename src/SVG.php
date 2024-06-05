<?php

namespace Northrook\Types;

use Northrook\Logger\Log;
use Northrook\Types\Interfaces\Printable;
use Stringable;
use SVG\SVG as SVGObject;

final class SVG implements Printable, Stringable
{

    public const MISSING = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16"><g fill="#E62419" opacity=".75"><path d="M5.4 1.35 3.68 2.37l.58.98 1.72-1.02-.58-.98Z"/><path d="M2.29 5.71H1.14v-1.2c0-.4.23-.8.57-.97l.86-.51.57.97-.85.51v1.2Z"/><path d="M2.29 6.86H1.14v2.28H2.3V6.86Z"/><path d="m2.57 12.97-.86-.51a1.18 1.18 0 0 1-.57-.97v-1.2H2.3v1.2l.85.51-.57.97Z"/><path d="m4.26 12.63-.58.99 1.72 1.01.58-.98-1.72-1.02Z"/><path d="m8.86 14.34-.86.52-.86-.52-.57.97.86.52c.17.11.4.17.57.17.23 0 .4-.06.57-.17l.86-.52-.57-.97Z"/><path d="m11.73 12.64-1.72 1.02.58.98 1.73-1.01-.59-.99Z"/><path d="m13.26 13.09-.57-.98 1.02-.57V10.3h1.15v1.2c0 .4-.23.8-.57.97l-1.03.63Z"/><path d="M14.86 6.86H13.7v2.28h1.15V6.86Z"/><path d="M14.86 5.71H13.7v-1.2l-1.02-.57.57-.97 1.03.57c.34.23.57.57.57.97v1.2Z"/><path d="m10.55 1.33-.58.98 1.72 1.02.59-.99-1.73-1.01Z"/><path d="M8.86 1.66 8 1.14l-.86.52-.57-.97.86-.52A.97.97 0 0 1 8 0c.23 0 .4.06.57.17l.86.52-.57.97Z"/></g><path fill="#F04B42" fill-rule="evenodd" d="M8 3c.5 0 1.15.26 1.4.95.09.2.12.44.09.66l-.5 3.94a1 1 0 0 1-1.98 0L6.5 4.6c-.03-.22 0-.45.08-.66C6.85 3.26 7.5 3 8 3Zm-1 9a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Z" clip-rule="evenodd"/></svg>';

    private function __construct( public readonly SVGObject $svg ) {
        trigger_deprecation(
            $this::class,
            '1.0.0',
            $this::class . ' is deprecated, use Northrook\Type instead',
        );
    }

    public static function file( string $path ) : SVG {
        if ( !file_exists( $path ) ) {
            Log::Error(
                'Requested SVG file does not exist: {path}',
                [ 'path' => $path ],
            );
            return new SVG( SVGObject::fromString( SVG::MISSING ) );
        }
        return new SVG( SVGObject::fromFile( $path ) );
    }

    public static function string( string $string ) : SVG {
        return new SVG( SVGObject::fromString( $string ) );
    }

    public function __toString() : string {
        return $this->print();
    }

    public function print() : string {
        $string = $this->svg->toXMLString();

        return str_replace( ' xmlns="http://www.w3.org/2000/svg"', '', $string );
    }
}