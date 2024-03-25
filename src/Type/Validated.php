<?php

declare( strict_types = 1 );


namespace Northrook\Types\Type;

/**
 * @access protected
 * @property bool $isValid
 */
abstract class Validated extends Type
{
    protected ?bool $isValid = null;

    abstract protected function validate() : bool;

    /**
     * @internal
     */
    protected function isValid() : bool {

        if ( $this->isValid ) {
            return true;
        }

        $this->isValid = $this->validate();

        return true;
    }
}