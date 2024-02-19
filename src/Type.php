<?php declare ( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Support\Debug;
use Northrook\Support\Str;
use Northrook\Types\Exception\InvalidTypeException;

/**
 * @property readonly $value
 * @property bool $isValid
 * */
abstract class Type {

	public readonly string $type;

	public const STRICT = false;
	public const TYPE   = 'undefined';

	protected mixed $value;
	protected bool $isValid;
	protected array $history = [];

	abstract static function type(): self;

	final protected function updateValue( mixed $value, bool $revalidate = true ): void {

		$this->value = $value;

		if ( $revalidate ) {
			$this->isValid = $this->validate();
		}
		

		if ( ! $this->isValid ) {
			if ( self::STRICT ) {
				throw new InvalidTypeException(
					'The type "' . $this::class . '" did not pass validation.',
					$this->value,
					$this::TYPE
				);
			} else if ( ! empty( $this->history ) ) {

				$last        = array_pop( $this->history );
				Debug::log(
					'The type "' . $this::class . '" did not pass validation, falling back to previous value.',
					[
						'failedValue'   => $this->value,
						'fallbackValue' => $last,
						'caller'        => debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS )[1],
						'type'          => $this,
					],
				);
				$this->value = $last;
			} else {
				Debug::log( 'The type "' . $this::class . '" did not pass validation.', 
				[
					'failedValue'   => $this->value,
					'caller'        => debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS )[1],
					'type'          => $this,
				], );
			}
		}

		// \var_dump( $this->value . ' is valid? ' . ($this->isValid  ? 'yes' : 'no') );

		$this->history[] = $this->value;

	}

	// public static function __callStatic( string $name, array $arguments ) {

	// 	if ( 'type' == strtolower( $name ) ) {
	// 		return new static( ...$arguments );
	// 	}

	// 	if ( method_exists( self::class, $name ) ) {

	// 		return self::{$name}( ...$arguments );
	// 	}
	// }

	final public function __get( ?string $name ) {
		if ( 'value' == strtolower( $name ) ) {
			return (string) $this;
		}
		if ( property_exists( $this, $name ) ) {
			return $this->{$name};
		}
	}

	final public function __toString() {

		if ( false === isset( $this->isValid ) ) {
			$this->isValid = $this->validate();
		}

		if ( is_string( $this->value ) ) {
			return $this->value;
		}

		return Str::asJson( $this->value ) ?? '';

	}

	/**
	 * @param mixed $value
	 * @param bool $validate Will validate against TYPE or custom validate() method
	 */
	protected function __construct( mixed $value = null, bool $validate = true, ...$vars ) {

		foreach ( $vars as $property => $assign ) {
			if ( property_exists( $this, $property ) ) {
				$this->{$property} = $assign;
			}
		}

		$this->updateValue( $value, $validate );
		$this->type = $this::TYPE;
		// if ( $validate ) {
		// 	$this->validate();
		// }
	}

	protected function validType( ?string $type = null ): bool {

		$value = strtolower( gettype( $this->value ) );

		if ( null === $type ) {
			$type = self::TYPE;
		} else {
			return $value === strtolower( $type );
		}

		$types = [];

		if ( str_contains( $type, '?' ) ) {
			$types[] = 'null';
		}

		$types = array_merge(
			$types,
			(array) explode(
				'|',
				\strtolower( str_replace( '?', '', $this::TYPE ) )
			)
		);

		return in_array( $value, $types );
	}

	protected function validate(): bool {

		$this->isValid = $this->validType();

		return $this->isValid;
	}

}