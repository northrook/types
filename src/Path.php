<?php declare ( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Support\Attribute\Development;
use Stringable;

#[Development( 'started' )]
class Path extends Type implements Stringable
{

	protected const TYPE = 'path';

	public static function type(
		?string $string = null,
	) : Path {
		return new static( $string );
	}

	protected function validate() : bool {
		$this->value = static::normalize( $this->value );

		return file_exists( $this->value );
	}

	public function add( string $string ) : Path {
		$this->updateValue( $this->value . $string );

		return $this;
	}

	/**
	 * @param  string  $string
	 * @return string
	 */
	public static function normalize( string $string ) : string {

		$string = mb_strtolower( strtr( $string, "\\", "/" ) );

		if ( str_contains( $string, '/' ) === false ) {
			return $string;
		}

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

		if ( false === isset( pathinfo( $path )[ 'extension' ] ) ) {
			$path .= DIRECTORY_SEPARATOR;
		}

		return $path;
	}
}