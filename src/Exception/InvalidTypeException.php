<?php declare ( strict_types = 1 );

namespace Northrook\Types\Exception;



use Exception;

/** The exception thrown when the value does not pass validation.
 * 
 */
class InvalidTypeException extends Exception {

	/**
	 * @param string $message — [optional] The Exception message to throw.
	 * @param mixed $value — [optional] The value that failed validation.
	 * @param null|string $type — [optional] The type that failed validation.
	 * @return void
	*/
	public function __construct( 
		string $message = '', 
		public readonly mixed $value = null,
		public readonly ?string $type = null,
		int $code = 422 ) {
		parent::__construct( $message, $code );
		$this->message = "$message";
		$this->code    = $code;
	}

}