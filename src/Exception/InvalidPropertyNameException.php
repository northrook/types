<?php

declare( strict_types = 1 );

namespace Northrook\Types\Exception;

use Exception;

/**
 */
final class InvalidPropertyNameException extends Exception
{
    final public function __construct(
        string                 $message,
        private readonly array $properties = [],
        int                    $code = 0,
        ?Exception             $previous = null,
    ) {
        parent::__construct( $message, $code, $previous );
    }

}