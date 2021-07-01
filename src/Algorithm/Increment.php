<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences\Algorithm;

use Sbooker\PersistentSequences\Algorithm;

final class Increment implements Algorithm
{
    private int $first;

    public function __construct(int $first = 0)
    {
        $this->first = $first;
    }

    public function next(string $currentValue): string
    {
        return (string) ((int) $currentValue + 1);
    }

    public function first(): string
    {
        return (string)$this->first;
    }
}