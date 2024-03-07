<?php

namespace Northrook\Types;

// TODO: https://www.php.net/manual/en/function.uniqid.php

use Northrook\Support\Attributes\Development;
use Stringable;

#[Development( 'started' )]
class ID extends Type implements Stringable
{

	public const TYPE = 'string';

//	protected string $value;
	protected bool   $cryptographicallySecure = false;
	protected bool     $quick                   = false;

	protected function validate() : bool {

		if ( str_ends_with( $this->value, 'uuid' ) ) {
			$this->value = uniqid( preg_split( ':', $this->value )[ 0 ], !$this->quick );
		}

		return true;
	}

	public static function from( string | array $value, ?string $separator = null ) : self {
		if ( is_array( $value ) ) {
			$value = implode(
				$separator ?? '.',
				array_filter( $value ),
			);
		}
		return new static(
			value                   : $value,
			validate                : true,
			cryptographicallySecure : false,
			quick                   : false
		);
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