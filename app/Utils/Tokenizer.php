<?php

namespace App\Utils;

use Exception;

class Tokenizer
{
    private int $cursor = 0;

    private string $data = '';

    public function __construct(private array $tokens)
    {
    }

    public function read(string $data): void
    {
        $this->cursor = 0;
        $this->data = $data;
    }

    /** @throws Exception */
    public function next(): array|null
    {
        if ($this->cursor === strlen($this->data)) {
            return null;
        }

        $str = substr($this->data, $this->cursor);

        foreach ($this->tokens as [$pattern, $type]) {
            $matches = [];
            $doesMatch = preg_match($pattern, $str, $matches);

            if (!$doesMatch) {
                continue;
            }

            $this->cursor += strlen($matches[0]);

            if ($type === null) {
                return $this->next();
            }

            return ['token' => $matches[0], 'type' => $type];
        }

        throw new Exception("Unrecognized input: $str[0]");
    }
}
