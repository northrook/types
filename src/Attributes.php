<?php

namespace Northrook\Types;

use Countable;
use IteratorAggregate;
use Traversable;

class Attributes implements TypeInterface
{

	public string | null $id    = null;
	public array | null  $class = [];
	public array | null  $style = [];
	private array        $data;


	private function __construct(
		string | null         $id,
		string | array | null $class = null,
		string | array | null $style = null,
		array | null          $data = null,
	) {
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

	public function __toString() {
		$classes = implode( ' ', $this->class );
		$styles = implode( '; ', $this->style );

		$attributes = [];

		foreach ( $this->data as $name => $value ) {
			$value = match ( gettype( $value ) ) {
				'string'  => $value,
				'boolean' => $value ? 'true' : 'false',
				'object'  => $value->__toString(),
				'array'   => implode( ' ', $value ),
				'NULL'    => null,
				default   => (string) $value,

			};

			$attributes[] = ( null === $value ) ? $name : "$name=\"$value\"";
		}

		return implode(
			' ', array_filter(
			[
				$this->id ? "id=\"$this->id\"" : null,
				$this->class ? "class=\"$classes\"" : null,
				$this->style ? "style=\"$styles\"" : null,
				...$attributes,
			],
		),
		);
	}

	public function __get( ?string $name ) : mixed {
		return $this->data[ $name ] ?? null;
	}
}