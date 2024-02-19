<?php declare ( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Types\Exception\InvalidTypeException;
use ZxcvbnPhp\Zxcvbn;

class Password extends Type {

	public const TYPE = 'string';

	private static int $defaultStrength = 3;

	protected readonly int $minimumStrength;
	protected readonly int $strength;
	protected array $context = [];
	public readonly array $score;


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

	private function minimumStrength(): int {
		return max( 0, min( 4, $this->minimumStrength ) );
	}

	protected function validate(): bool {

		$validator = new Zxcvbn();

		$this->score = $validator->passwordStrength( $this->value ?? '', $this->context );

		$this->strength = $this->score['score'] ?? 0;

		if ( $this->strength < $this->minimumStrength() ) {
			$this->isValid = false;
		} else {
			$this->isValid = true;
		}

		return $this->isValid;
	}

}