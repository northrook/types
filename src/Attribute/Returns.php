<?php

namespace Northrook\Types\Attribute;

use Attribute;
use JetBrains\PhpStorm\ExpectedValues;

#[Attribute(
	Attribute::TARGET_ALL
)]
class Returns
{
	public const TYPE = [
		'?array',
		'array',
		'?bool',
		'bool',
		'?int',
		'int',
		'?float',
		'float',
		'?string',
		'string',
		'?object',
		'object',
		'?iterable',
		'iterable',
		'?callable',
		'callable',
		'void',
		'null',
	];

	public function __construct(
		#[ExpectedValues( self::TYPE )]
		string $type,
	) {}
}