<?php

namespace Northrook\Types;

use JetBrains\PhpStorm\ExpectedValues;
use Northrook\Core\PasswordValidator;
use Northrook\Types\Interfaces\Printable;
use Northrook\Types\Internal\Options;
use Northrook\Types\Traits\PrintableTypeTrait;
use Northrook\Types\Type\Validated;
use SensitiveParameter;


/**
 */
class Password extends Validated implements Printable
{
    use PrintableTypeTrait;

    public readonly PasswordValidator $validator;
    public readonly int               $strength;
    private readonly int              $minimumStrength;

    public function __construct(
        #[SensitiveParameter]
        string                 $password,
        #[SensitiveParameter]
        private readonly array $context = [],
        #[ExpectedValues( [ 0, 1, 2, 3, 4 ] )]
        ?int                   $minimumStrength = null,
    ) {
        $this->value = $password;

        $minimumStrength ??= Options::get( 'password' )[ 'minimumStrength' ] ?? 3;

        $this->minimumStrength = max( 0, min( 4, $minimumStrength ) );


        parent::__construct();
    }

    protected function validate() : bool {

        $this->validator = new PasswordValidator( $this->value, $this->context );
        $this->strength  = $this->validator->strength;

        if ( $this->strength < $this->minimumStrength ) {
            return false;
        }

        return true;
    }
}