<?php

namespace Northrook\Types\Type;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use stdClass;
use Traversable;

abstract class Record extends stdClass implements Countable, IteratorAggregate
{
	private array $records;

	final public function __invoke() : array {
		return $this->records;
	}

	public static function create( array $array = [] ) : static {
		return ( new static() )->clear( new : $array );
	}

	public function add(
		Record | array | string $value,
	) : static {
		if ( $value instanceof Record ) {
			$value = $value->get();
		}
		$this->records = array_merge( $this->records, (array) $value );

		return $this;
	}

	public function set( string | array $value ) : static {
		$this->records = (array) $value;

		return $this;
	}

	public function get( string | int | null $key = null, mixed $default = null ) : mixed {
		if ( $key === null ) {
			return $this->records;
		}
		return $this->records[ $key ] ?? $default;
	}

	public function remove( string | int | null $key, mixed $value = null ) : static {
		if ( $key ) {
			unset( $this->records[ $key ] );
		}

		if ( $value ) {
			$this->records = array_filter( $this->records, fn ( $item ) => $item !== $value );
		}

		return $this;
	}

	public function joink( string | int $key ) : mixed {
		if ( !$this->has( $key ) ) {
			return null;
		}
		$grab = $this->get( $key );
		$this->remove( $key );
		return $grab;
	}

	public function has( string | int | null $key, mixed $value = null ) : bool {
		if ( $key ) {
			return isset( $this->records[ $key ] );
		}

		if ( $value ) {
			return in_array( $value, $this->records );
		}

		return false;
	}

	public function count() : int {
		return count( $this->records );
	}

	public function clear( array $new = [] ) : static {
		$this->records = $new;
		return $this;
	}

	public function getIterator() : Traversable {
		return new ArrayIterator( $this->records );

	}


}