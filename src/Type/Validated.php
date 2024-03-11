<?php

namespace Northrook\Types\Type;

/**
 * @access protected
 * @property bool $isValid
 */
abstract class Validated extends Type
{

    protected readonly bool $isValid;

    abstract protected function validate() : bool;

    /**
     * @internal
     */
    protected function isValid() : bool {
        return $this->isValid ??= $this->validate();
    }
}