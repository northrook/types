<?php

namespace Northrook\Type\Interface;

/**
 * Encompasses both {@see Path} and {@see URL}.
 */
interface PathType extends StringType
{
    public function validate() : bool;
}