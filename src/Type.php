<?php declare ( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Logger\Log;
use Northrook\Support\Attribute\Development;
use Northrook\Support\Str;
use Northrook\Types\Exception\InvalidTypeException;
use stdClass;

/**
 * @property $value
 * @property bool $isValid
 * */
#[Development( 'static' )]
abstract class Type extends stdClass
{
	protected const TYPE   = null;
	protected const STRICT = false;

	public readonly string $type;


	protected mixed $value;
	protected bool  $isValid;
	protected array $history = [];

	abstract public static function type() : self;

	/**
	 * @param  mixed  $value
	 * @param  bool  $validate  Will validate against TYPE or custom validate() method
	 * @param  mixed  ...$vars
	 */
	protected function __construct( mixed $value = null, bool $validate = true, ...$vars ) {
		$this->assignType( $value );
		$this->assignVariables( $vars );
		$this->updateValue( $value, $validate );
	}

	private function assignType( mixed $value ) : void {
		$this->type = strtolower( $this::TYPE ?? gettype( $value ) );
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
					$this->type,
				);

				Log::error( 'The type {class} did not pass validation.', [
					'class'     => $this::class,
					'exception' => $exception,
				] );

			}
			else {
				if ( !empty( $this->history ) ) {

					$last = array_pop( $this->history );

					Log::Warning(
						'The type "{class}" did not pass validation. Falling back to previous value {previous}.',
						[
							'class'    => $this::class,
							'previous' => $last,
							'type'     => $this,
						],
					);
				}
				else {
					Log::Warning(
						'The value {value} did not pass validation in type "{class}".',
						[
							'class' => $this::class,
							'value' => $this->value,
							'type'  => $this,
						],
					);
				}
			}
		}

		$this->history[] = $this->value;

	}

	final public function __get( ?string $name ) : mixed {
		if ( 'value' == strtolower( $name ) ) {
			return $this->__toString();
		}
		if ( property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		return null;
	}

	public function __toString() : string {

		if ( false === isset( $this->isValid ) ) {
			$this->isValid = $this->validate();
		}

		if ( is_string( $this->value ) ) {
			return $this->value;
		}

		return Str::asJson( $this->value ) ?? '';

	}

	protected function validType( ?string $type = null ) : bool {

		if ( null === $type ) {
			$type = $this->type;
		}
		else {
			return $this->type === strtolower( $type );
		}

		$types = [];

		if ( str_contains( $type, '?' ) ) {
			$types[] = 'null';
		}

		$types = array_merge(
			$types,
			explode(
				'|',
				strtolower( str_replace( '?', '', $this->type ) ),
			),
		);

		return in_array( $this->type, $types );
	}

	protected function validate() : bool {

		$this->isValid = $this->validType();

		return $this->isValid;
	}

}