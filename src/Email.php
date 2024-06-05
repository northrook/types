<?php

declare( strict_types = 1 );


namespace Northrook\Types;

use Northrook\Core\EmailValidator;
use Northrook\Types\Interfaces\Printable;
use Northrook\Types\Traits\PrintableTypeTrait;
use Northrook\Types\Type\Validated;


class Email extends Validated implements Printable
{
    use PrintableTypeTrait;

    public readonly EmailValidator $validator;
    public readonly string         $username;
    public readonly string         $domain;
    public readonly string         $tld;

    public function __construct(
        string $email,
    ) {
        trigger_deprecation(
            $this::class,
            '1.0.0',
            $this::class . ' is deprecated, use Northrook\Type instead',
        );

        $this->value = $email;

        parent::__construct();
    }


    protected function validate() : bool {
        $this->validator = new EmailValidator( $this->value );

        if ( $this->validator->isValid ) {
            $this->explodeEmail();
        }

        return $this->validator->isValid;
    }

    private function explodeEmail() : void {

        $email          = explode( '@', (string) $this->value );
        $this->username = $email[ 0 ];
        $this->domain   = strstr( $email[ 1 ], '.', true );
        $this->tld      = substr( $email[ 1 ], strpos( $email[ 1 ], '.' ) + 1 );

    }

}