<?php

namespace App\Utils;

use Exception;

class ExpressionParser {
    private static array $tokens = [
        ["/^\s+/", null],
        ["/^-?\d+(?:\.\d+)?\b/", 'NUMBER'],
        ["/^[a-zA-Z]+/", 'IDENT'],
        ["/^:[a-zA-Z]+(\.[a-zA-Z]+)*/", 'ATTRIBUTE'],
        ["/^\"[^\"]*\"/", 'STRING'],
        ["/^\+/", '+'],
        ["/^-/", '-'],
        ["/^\*/", '*'],
        ["/^\^/", '^'],
        ["/^>/", '>'],
        ["/^</", '<'],
        ["/^=/", '='],
        ["/^\//", '/'],
        ["/^\(/", '('],
        ["/^\)/", ')'],
        ["/^,/", ','],
    ];

    private array|null $lookahead = null;
    private Tokenizer $tokenizer;

    public function __construct() {
        $this->tokenizer = new Tokenizer(self::$tokens);
    }

    /** @throws Exception */
    public function read($string): array {
        $this->tokenizer->read($string);
        $this->lookahead = $this->tokenizer->next();

        $expression = $this->EXPRESSION();

        if ($this->lookahead !== null) {
            throw new Exception("Unexpected continuation of input");
        }

        return $expression;
    }

    public function eat(string $tokenType, ...$tokenTypes): array {
        $token = $this->lookahead;

        if ($token === null || $this->lookahead === null) {
            throw new Exception("Unexpected end of input; expected $tokenType");
        }

        if (!in_array($this->lookahead["type"], [$tokenType, ...$tokenTypes])) {
            throw new Exception("Expected $tokenType === {$token["type"]}");
        }

        $this->lookahead = $this->tokenizer->next();

        return $token;
    }

    private function is(...$tokenTypes): bool {
        return $this->lookahead !== null && in_array($this->lookahead["type"], $tokenTypes);
    }

    private function EXPRESSION(): array {
        return $this->COMPARISON();
    }

    private function COMPARISON(): array {
        $left = $this->ADDITION();

        while ($this->is('=', '<', '>')) {
            $left = [
                'type' => 'binary',
                'op' => $this->eat('=', '<', '>')["type"],
                'left' => $left,
                'right' => $this->ADDITION(),
            ];
        }

        return $left;
    }

    private function ADDITION(): array {
        $left = $this->MULTIPLICATION();

        while ($this->is('+', '-')) {
            $left = [
                'type' => 'binary',
                'op' => $this->eat('+', '-')["type"],
                'left' => $left,
                'right' => $this->MULTIPLICATION(),
            ];
        }

        return $left;
    }

    private function MULTIPLICATION(): array {
        $left = $this->EXPONENTIATION();

        while ($this->is('*', '/')) {
            $left = [
                'type' => 'binary',
                'op' => $this->eat('*', '/')["type"],
                'left' => $left,
                'right' => $this->EXPONENTIATION(),
            ];
        }

        return $left;
    }

    private function EXPONENTIATION(): array {
        $left = $this->BASIC();

        while ($this->is('^')) {
            $left = [
                'type' => 'binary',
                'op' => $this->eat('^')["type"],
                'left' => $left,
                'right' => $this->BASIC(),
            ];
        }

        return $left;
    }

    private function BASIC(): array {
        if ($this->is('(')) {
            $this->eat('(');
            $expr = $this->EXPRESSION();
            $this->eat(')');

            return $expr;
        }

        if ($this->is('ATTRIBUTE')) {
            $attribute = $this->eat('ATTRIBUTE');
            return ['type' => 'attribute', 'value' => substr($attribute['token'], 1)];
        }

        if ($this->is('IDENT')) {
            $identifier = $this->eat('IDENT');

            if ($this->is('(')) {
                $this->eat('(');
                $args = [$this->EXPRESSION()];

                while ($this->is(',')) {
                    $this->eat(',');
                    $args[] = $this->EXPRESSION();
                }

                $this->eat(')');
                return ['type' => 'call', 'fn' => $identifier["token"], 'args' => $args];
            }

            return ['type' => 'identifier', 'value' => $identifier["token"]];
        }

        if ($this->is('NUMBER')) {
            return ['type' => 'number', 'value' => $this->eat('NUMBER')["token"]];
        }

        if ($this->is('STRING')) {
            return ['type' => 'string', 'value' => substr($this->eat('STRING')["token"], 1, -1)];
        }

        throw new Exception('Malformed expression.');
    }
}
