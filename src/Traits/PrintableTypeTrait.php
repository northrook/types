<?php

declare( strict_types = 1 );

namespace Northrook\Types\Traits;

use Northrook\Logger\Log;

/**
 * @property string $value
 */
trait PrintableTypeTrait
{
    public function __toString() : string {
        return $this->print();
    }

    public function print() : string {
        if ( method_exists( $this, 'isValid' ) && false === $this->isValid() ) {
            Log::error(
                'The type {class} was printed, but did not pass validation.', [
                'class' => $this::class,
                'value' => $this->value,
                'type'  => $this,
            ],
            );
        }
        return $this->value;
    }
}