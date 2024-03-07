<?php declare(strict_types=1);


namespace Northrook\Types\Type;

use Countable;
use stdClass;


/**
 * Properties and options.
 *
 * @author  Martin Nielsen <mn@northrook.com>
 *
 * @link    https://github.com/northrook Documentation
 * @todo    Update URL to documentation
 */
abstract class Properties extends stdClass implements Countable
{
	public function __construct(
		array $set,
	) {
		foreach ( $set as $key => $value ) {
			$this->$key = $value;
		}
	}

	public function __get( string $name ) {
		if ( $this->has( $name ) ) {
			return $this->$name;
		}
		return null;
	}

	public function __set( string $name, mixed $value ) {
		$this->$name = $value;
	}

	/**
	 * Resets the properties to a new set.
	 *
	 * * Each existing property will be removed.
	 * * Each new property will be added.
	 *
	 * @param  array  $new
	 * @return $this
	 */
	public function reset( array $new ) : self {
		foreach ( $this as $currentKey => $currentValue ) {
			unset( $this->$currentKey );
		}
		foreach ( $new as $newKey => $newValue ) {
			$this->$newKey = $newValue;
		}
		return $this;

	}

	public function add( string $key, mixed $value, bool $override = false ) : self {

		if ( !$override && $this->has( $key ) ) {
			return $this;
		}

		$this->$key = $value;

		return $this;
	}

	public function has( string $key ) : bool {
		return isset( $this->$key );
	}

	public function remove( string $key ) : self {
		if ( $this->has( $key ) ) {
			unset( $this->$key );
		}
		return $this;
	}

	public function count() : int {
		return count( $this );
	}

}