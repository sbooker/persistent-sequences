<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences;

class Sequence
{
    private string $name;

    private string $value;

    public function __construct(string $sequenceName, Algorithm $algorithm)
    {
        $this->name = $sequenceName;
        $this->value = $algorithm->first();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function updateValue(Algorithm $algorithm): void
    {
        $this->value = $algorithm->next($this->value);
    }
}