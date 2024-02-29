<?php

namespace Northrook\Types;

class ID extends Type
{

	public const TYPE = 'string';

	protected string $value;

	public function __construct( string $value ) {}

	static function type( ?string $value = null ) : ID {
		return new static( $value );
	}
}