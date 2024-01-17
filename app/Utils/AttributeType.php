<?php

namespace App\Utils;

use Exception;

class AttributeType
{
    public readonly string $name;

    public readonly ?int $entityId;

    /**
     * @throws Exception
     */
    public function __construct(private readonly string $value)
    {
        if (in_array($this->value, ['string', 'number', 'boolean'])) {
            $this->name = $this->value;

            return;
        }

        $matches = [];
        $doesMatch = preg_match('/^entity:(\d+)$/', $value, $matches);
        if ($doesMatch) {
            $this->name = 'entity';
            $this->entityId = $matches[1];

            return;
        }

        $matches = [];
        $doesMatch = preg_match('/^collection:(\d+)$/', $value, $matches);
        if ($doesMatch) {
            $this->name = 'collection';
            $this->entityId = $matches[1];

            return;
        }

        throw new Exception("Invalid type: $this->value");
    }
}
