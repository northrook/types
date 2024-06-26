<?php

declare ( strict_types = 1 );

namespace Northrook\Types;

use Northrook\Types\Interfaces\Printable;
use Northrook\Types\Traits\PrintableTypeTrait;
use Northrook\Types\Type\Validated;

class Url extends Validated implements Printable
{
    use PrintableTypeTrait;

    private static string  $rootPath;
    protected bool         $isAbsolute = false;
    public readonly string $path;
    public readonly string $url;

    public function __construct(
        string $path,
    ) {
        trigger_deprecation(
            $this::class,
            '1.0.0',
            $this::class . ' is deprecated, use Northrook\Type instead',
        );


        // What we start with
        $this->value = $path;

        // Validate if refers to local, or external
        // Validate endpoint exists
        // Cache endpoint in a file? We don't want to keep revalidating each time
        // Revalidate if endpoint changes, or after a certain amount of time (like a day)

        parent::__construct();
    }


    protected function validate() : bool {
        return true;
    }
}