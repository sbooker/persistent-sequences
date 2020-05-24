<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences;

interface Algorithm
{
    public function next(string $currentValue): string;

    public function first(): string;
}