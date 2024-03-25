<?php
declare( strict_types = 1 );

namespace Northrook\Types\Type;

//
//use ArrayIterator;
//use Countable;
//use IteratorAggregate;
//use Traversable;
//
//class Collection implements Countable, IteratorAggregate
//{
//
//	private int   $position = 0;
//	private array $keys     = [];
//	private array $records  = [];
//
//	public function __construct( array $array ) {
//
//		foreach ( $array as $key => $value ) {
//			$index = count( $this->records );
//			$key = is_string( $key ) ? $key : $index;
//			$this->keys[ $index ] = $key;
//			$this->records[] = new Record(
//				key   : $key,
//				value : $value,
//			);
//		}
//
//	}
//
//	public static function create( array $array = [] ) : static {
//		return new static( $array );
//	}
//
//	public static function fromMap( array $items, callable $fn ) : static {
//		return new static( array_map( $fn, $items ) );
//	}
//
//	/**
//	 * Count the number of elements in the collection.
//	 *
//	 * * Counts active records.
//	 *
//	 * @return int
//	 */
//	public function count() : int {
//		return count( $this->records );
//	}
//
//	public function records() : array {
//		return $this->records;
//	}
//
//	private function asArray() : array {
//		$array = [];
//		foreach ( $this->records as $record ) {
//			$array[ $record->key() ] = $record->value();
//		}
//		return $array;
//	}
//
//
//	/**
//	 * Get a record from the collection.
//	 *
//	 * @param  mixed|null  $key
//	 * @param  mixed|null  $value
//	 * @param  mixed|null  $default
//	 * @return mixed
//	 */
//	public function get(
//		mixed $key = null,
//		mixed $value = null,
//		mixed $default = null,
//	) : mixed {
//
//		if ( $key === null && $value === null ) {
//			return $this->asArray();
//		}
//
//		$get = null;
//
////		// If we get by $key, ensure it exists, otherwise keep null
////		if ( $key && array_key_exists( $key, $this->array ) ) {
////			$get = $this->array[ $key ];
////		}
////
////		// If we get by both $key and $value, treat it as "if key has value that is equal to $value"
////		if ( $key && $value ) {
////			return (bool) $get === $value;
////		}
////
////		if ( $value ) {
////			$index = array_search( $value, $this->array );
////			dump( "index: $index" );
////		}
////
////		if ( $get === null ) {
////			return $default;
////		}
//
//
//		return '$get';
//	}
////
////	public function reduce( callable $fn, mixed $initial ) : mixed {
////		return array_reduce( $this->array, $fn, $initial );
////	}
////
////	public function map( callable $fn ) : array {
////		return array_map( $fn, $this->array );
////	}
////
////	public function each( callable $fn ) : void {
////		array_walk( $this->array, $fn );
////	}
////
////	public function some( callable $fn ) : bool {
////		foreach ( $this->array as $index => $element ) {
////			if ( $fn( $element, $index, $this->array ) ) {
////				return true;
////			}
////		}
////
////		return false;
////	}
////
////	public function filter( callable $fn ) : static {
////		return new static( array_filter( $this->array, $fn, ARRAY_FILTER_USE_BOTH ) );
////	}
////
////	public function first() : mixed {
////		return reset( $this->array );
////	}
////
////	public function last() : mixed {
////		return end( $this->array );
////	}
////
////	public function count() : int {
////		return count( $this->array );
////	}
////
////	public function isEmpty() : bool {
////		return empty( $this->array );
////	}
////
////	public function add( mixed $element ) : void {
////		$this->array[] = $element;
////	}
////
////	public function values() : array {
////		return array_values( $this->array );
////	}
////
////	public function items() : array {
////		return $this->array;
////	}
////
//	public function getIterator() : Traversable {
//		return new ArrayIterator( $this->records );
//	}
//
//	public function current() : mixed {
//		return $this->records[ $this->position ];
//	}
//
//}


use Countable;
use Iterator;
use JetBrains\PhpStorm\Deprecated;
use ReturnTypeWillChange;
use Traversable;

#[Deprecated(
    'Still under review. Use Record where able, and log edge cases in Focus.',
    Record::class
)]
class Collection implements Countable, Traversable, Iterator
{


    private int   $position = 0;
    private array $array    = [];


    public function __construct( array $array = [] ) {
        $this->array = $array;
//		dump( $this);
    }

    public static function create( array $array = [] ) : static {
        return new static( $array );
    }

    public function add( array $value ) : void {
        $this->array = array_merge( $this->array, $value );
    }

    public function count() : int {
        return count( $this->array );
    }

    public function rewind() : void {
        $this->position = 0;
    }

    #[ReturnTypeWillChange]
    public function current() : mixed {
        return $this->array[ $this->position ];
    }

    public function key() : int {
        return $this->position;
    }


    public function next() : void {
        ++$this->position;
    }

    public function previous() : void {
        --$this->position;
    }

    public function valid() : bool {
        return isset( $this->array[ $this->position ] );
    }
}