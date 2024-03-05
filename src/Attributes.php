<?php

namespace Northrook\Types;

use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
class Attributes
{

	public string | null         $id      = null;
	public string | array | null $class   = [];
	public string | array | null $style   = [];
	private array                $data;
	public ?bool                 $isValid = null;


	private function __construct(
		string | null         $id,
		string | array | null $class = null,
		string | array | null $style = null,
		array | null          $data = null,
	) {
		dd($this);
		$this->id = $id;
		$this->class = is_string( $class ) ? explode( ' ', $class ) : $class ?? [];
		$this->style = is_string( $style ) ? explode( ';', $style ) : $style ?? [];

		$this->data = $data;
	}

	public static function type(
		string | null         $id,
		string | array | null $class,
		string | array | null $style,
		array                 ...$data
	) : Attributes {
		return new attributes( $id, $class, $style, $data );
	}

	public function __invoke() : array {
		return [
			       'id'    => $this->id,
			       'class' => $this->class,
			       'style' => $this->style,
		       ] + $this->getDataAttributes();
	}

	private function getDataAttributes() : array {
		$attributes = [];
		foreach ( $this->data as $name => $value ) {
			$value = match ( gettype( $value ) ) {
				'string'  => $value,
				'boolean' => $value ? 'true' : 'false',
				'object'  => $value->__toString(),
				'array'   => implode( ' ', array_filter( $value ) ),
				'NULL'    => null,
				default   => (string) $value,
			};

			if ( in_array( $name, [ 'disabled', 'readonly', 'required', 'checked', 'hidden' ] ) ) {
				$value = null;
			}

			$attributes[] = ( null === $value ) ? $name : "$name=\"$value\"";
		}

		return $attributes;
	}

	public function __toString() {

		if ( !is_string( $this->class ) ) {
			$this->class = implode( ' ', $this->class );
		}

		if ( !is_string( $this->style ) ) {
			$this->style = implode( '; ', $this->style );
		}

		return implode(
			' ', array_filter(
			[
				$this->id ? "id=\"$this->id\"" : null,
				$this->class ? "class=\"$this->class\"" : null,
				$this->style ? "style=\"$this->style\"" : null,
				...$this->getDataAttributes(),
			],
		),
		);
	}

	public function get( string $name ) : mixed {
		return $this->data[ $name ] ?? null;
	}

	public function set( string $name, mixed $value, bool $override = true ) : self {

		if ( !$override && $this->has( $name ) ) {
			return $this;
		}

		if ( $name === 'id' ) {
			$this->id = $value;
		}

		if ( $name === 'class' ) {
			$classes = is_string( $value ) ? explode( ' ', $value ) : $value ?? [];
			$this->class = array_merge( $this->class, $classes );
		}

		if ( $name === 'style' ) {
			$styles = is_string( $value ) ? explode( ' ', $value ) : $value ?? [];
			$this->style = array_merge( $this->style, $styles );
		}

		$this->data[ $name ] = $value;

		return $this;
	}

	public function has( string | array $name ) : bool {
		if ( is_string( $name ) ) {
			return isset( $this->data[ $name ] );
		}

		foreach ( $name as $key => $boolean ) {
			if ( is_string( $key ) ) {
				return isset( $this->data[ $key ] ) === $boolean;
			}

			return isset( $this->data[ $key ] );
		}

		return false;
	}

	public function __get( ?string $name ) : mixed {
		return $this->data[ $name ] ?? null;
	}
}