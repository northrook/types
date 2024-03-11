<?php

namespace Northrook\Types\Interfaces;

/**
 *
 */
interface ValidatedType
{
    public function isValid() : bool;
}