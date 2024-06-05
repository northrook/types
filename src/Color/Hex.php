<?php

namespace Northrook\Types\Color;

use Northrook\Logger\Log;
use Northrook\Types\Interfaces\Printable;
use Northrook\Types\Type\Type;
use Stringable;

class Hex extends Type implements Printable, Stringable
{

    public function __construct(
        string $color,
    ) {
        trigger_deprecation(
            $this::class,
            '1.0.0',
            $this::class . ' is deprecated, use Northrook\Type instead',
        );
        
        $this->value = strtolower( $color );

        if ( $this->value[ 0 ] !== '#' ) {
            $this->value = '#' . $this->value;
        }

        if ( strlen( $this->value ) !== 7 ) {
            Log::Error(
                'The type {class} was created, but did not pass validation.',
                [
                    'class' => 'Color\Hex',
                    'value' => $this->value,
                    'type'  => $this,
                ],
            );
        }

        parent::__construct();
    }

    public function print() : string {
        return $this->value;
    }

    public function __toString() : string {
        return $this->print();
    }
}