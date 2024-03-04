<?php

namespace Northrook\Types;

use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
interface TypeInterface
{
	public const STRICT = false;
	public const TYPE   = null;


	public function __get( ?string $name ) : mixed;

	public function __toString();
}