<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences\Algorithm;

use Sbooker\PersistentSequences\Algorithm;

final class Increment implements Algorithm
{
    public function next(string $currentValue): string
    {
        return (string) ((int) $currentValue + 1);
    }

    public function first(): string
    {
        return '0';
    }
}