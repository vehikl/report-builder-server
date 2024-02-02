<?php

namespace App\Utils\PhpAttributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Dependencies
{
    /** @var string[] */
    public readonly array $paths;

    public function __construct(string ...$paths)
    {
        $this->paths = $paths;
    }
}
