<?php declare ( strict_types = 1 );

namespace Northrook\Types;

use ZxcvbnPhp\Zxcvbn;

class Password extends Type {

	private static int $defaultStrength = 3;

	protected int $minimunStrength;
	protected readonly int $strength;
	protected array $context = [];
	public readonly array $score;

	public const TYPE = 'string';

	public static function setDefaultStrength( int $strength ): void {
		self::$defaultStrength = $strength;
	}

	public static function type(
			?string $string = null,
			?int $strength = null,
		array $context = [],
		bool $validate = true
	): Password {
		return new static(
			value: $string,
			validate: $validate,
			minimunStrength: $strength ?? self::$defaultStrength,
			context: $context,
		);
	}

	private function minimunStrength(): int {
		return max( 0, min( 4, $this->minimunStrength ) );
	}

	protected function validate(): bool {

		$validator = new Zxcvbn();

		$this->score = $validator->passwordStrength( $this->value ?? '', $this->context );

		$this->strength = $this->score['score'] ?? 0;

		if ( $this->strength < $this->minimunStrength() ) {
			$this->isValid = false;
		} else {
			$this->isValid = true;
		}

		return $this->isValid;
	}

}