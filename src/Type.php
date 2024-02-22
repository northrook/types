<?php declare ( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Support\Debug;
use Northrook\Support\Str;
use Northrook\Types\Exception\InvalidTypeException;

/**
 * @property $value
 * @property bool $isValid
 * */
abstract class Type
{

	public readonly string $type;

	public const STRICT = false;
	public const TYPE   = 'undefined';

	protected mixed $value;
	protected bool  $isValid;
	protected array $history = [];

	abstract static function type() : self;

	/**
	 * @param  mixed  $value
	 * @param  bool  $validate  Will validate against TYPE or custom validate() method
	 * @param  mixed  ...$vars
	 */
	protected function __construct( mixed $value = null, bool $validate = true, ...$vars ) {
		$this->assignVariables( $vars );
		$this->updateValue( $value, $validate );


		$this->type = $this::TYPE;
	}


	final protected function assignVariables( array $vars ) : void {
		foreach ( $vars as $property => $assign ) {
			if ( property_exists( $this, $property ) ) {
				$this->{$property} = $assign;
			}
		}
	}

	final protected function updateValue( mixed $value, bool $revalidate = true ) : void {

		$this->value = $value;

		if ( $revalidate ) {
			$this->isValid = $this->validate();
		}


		if ( !$this->isValid ) {

			if ( self::STRICT ) {
				$exception = new InvalidTypeException(
					'The type "' . $this::class . '" did not pass validation.',
					$this->value,
					$this::TYPE
				);

				Debug::log( 'The type "' . $this::class . '" did not pass validation.', $exception, 'fatal' );

			}
			else {
				if ( !empty( $this->history ) ) {

					$last = array_pop( $this->history );
					Debug::log(
						'The type "' . $this::class . '" did not pass validation, falling back to previous value.',
						[
							'failedValue'   => $this->value,
							'fallbackValue' => $last,
							'caller'        => debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS )[ 1 ],
							'type'          => $this,
						],
					);
					$this->value = $last;
				}
				else {
					Debug::log(
						'The type "' . $this::class . '" did not pass validation.',
						[
							'failedValue' => $this->value,
							'caller'      => debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS )[ 1 ],
							'type'        => $this,
						],
					);
				}
			}
		}

		$this->history[] = $this->value;

	}

	final public function __get( ?string $name ) {
		if ( 'value' == strtolower( $name ) ) {
			return $this->__toString();
		}
		if ( property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		return null;
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

	protected function validType( ?string $type = null ) : bool {

		$value = strtolower( gettype( $this->value ) );

		if ( null === $type ) {
			$type = self::TYPE;
		}
		else {
			return $value === strtolower( $type );
		}

		$types = [];

		if ( str_contains( $type, '?' ) ) {
			$types[] = 'null';
		}

		$types = array_merge(
			$types,
			explode(
				'|',
				strtolower( str_replace( '?', '', $this::TYPE ) ),
			),
		);

		return in_array( $value, $types );
	}

	protected function validate() : bool {

		$this->isValid = $this->validType();

		return $this->isValid;
	}

}