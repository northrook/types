<?php

namespace Northrook\Types\Interfaces;

interface Printable
{
    /**
     * Prints the resulting HTML.
     *
     * Must handle all parsing, optimization, escaping, and encoding.
     *
     * @return string
     */
    public function print() : string;
}