<?php

namespace Northrook\Types;

use Northrook\Support\Attribute\Development;

#[Development( 'pending' )]
class Asset extends Type
{

	public const TYPE = 'object';

	protected Path $path;

	public function __construct( string $path ) {
		$this->path = new Path( $path );
	}

	static function type( ?string $path = null ) : Asset {
		return new static( $path );
	}
}