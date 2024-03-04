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
}