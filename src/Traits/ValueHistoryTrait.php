<?php

declare( strict_types = 1 );

namespace Northrook\Types\Traits;

use Northrook\Logger\Log;

/**
 * @property mixed $value
 */
trait ValueHistoryTrait
{
    protected array $history = [];

    public function updateValue( $value ) : void {
        $this->value = $value;

        if ( method_exists( $this, 'validate' ) && !$this->validate() ) {
            if ( !empty( $this->history ) ) {

                $last = array_pop( $this->history );

                Log::Warning(
                    'The type "{class}" did not pass validation. Falling back to previous value {previous}.',
                    [
                        'class'    => $this::class,
                        'previous' => $last,
                        'type'     => $this,
                    ],
                );
            }
            else {
                Log::Warning(
                    'The value {value} did not pass validation in type "{class}".',
                    [
                        'class' => $this::class,
                        'value' => $this->value,
                        'type'  => $this,
                    ],
                );
            }
        }

        $this->history[] = $this->value;
    }

    public function getHistory() : array {
        return $this->history;
    }
}