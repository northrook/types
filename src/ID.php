<?php

namespace Northrook\Types;

// TODO: https://www.php.net/manual/en/function.uniqid.php

class ID extends Type
{

	public const TYPE = 'string';

	protected string $value;
	protected bool   $cryptographicallySecure = false;
	private bool     $quick                   = false;

	protected function validate() : bool {

		if ( str_ends_with( $this->value, 'uuid' ) ) {
			$this->value = uniqid( preg_split( ':', $this->value )[ 0 ], !$this->quick );
		}

		return true;
	}

	/**
	 * * Apply a prefix to generated UUID with `prefix:uuid`
	 *
	 * @param  string|null  $value  Will generate a UUID if not provided
	 * @param  bool  $cryptographicallySecure  Will generate a cryptographically secure ID
	 * @param  bool  $quick  Uses $more_entropy=false
	 * @return ID
	 */
	static function type( ?string $value = 'uuid', bool $cryptographicallySecure = false, bool $quick = false ) : ID {
		return new static(
			value                   : $value ?? 'uuid',
			validate                : true,
			cryptographicallySecure : $cryptographicallySecure,
			quick                   : $quick
		);
	}
}