<?php declare ( strict_types = 1 );

namespace Northrook\Types;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\Extra\SpoofCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use LogicException;
use Northrook\Support\Attributes\Development;
use Northrook\Types\Type\Type;
use Stringable;

// TODO: Support blacklist and whitelist words, domains and tlds

#[Development( 'mvp' )]
class Email extends Type implements Stringable
{

	public const TYPE = '?string';

	protected string $username;
	protected string $domain;
	protected string $tld;

	public function update( ?string $value ) : Email {
		$this->updateValue( $value );

		return $this;
	}

	public static function type( ?string $string = null, bool $validate = true ) : self {
		return new static( $string, $validate );
	}

	private function explodeEmail() : void {

		$email = explode( '@', (string) $this->value );
		$this->username = $email[ 0 ];
		$this->domain = strstr( $email[ 1 ], '.', true );
		$this->tld = substr( $email[ 1 ], strpos( $email[ 1 ], '.' ) + 1 );

	}

	protected function validate() : bool {

		if ( $this->validType( 'null' ) ) {
			return true;
		}

		if ( !class_exists( EmailValidator::class ) ) {
			throw new LogicException(
				'EmailValidator not installed. Ensure that the "egulias/email-validator" package is installed.'
			);
		}

		$validator = new EmailValidator();

		$validate = new MultipleValidationWithAnd( [
			                                           new NoRFCWarningsValidation(),
			                                           new SpoofCheckValidation(),
		                                           ] );

		// \var_dump($validator);

		$this->isValid = $validator->isValid(
			$this->value ?? '',
			$validate,
		);

		if ( $this->isValid ) {
			$this->explodeEmail();
		}

		return $this->isValid;
	}

}