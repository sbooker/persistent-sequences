<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences;

final class SequenceReader
{
    private SequenceReadStorage $readStorage;

    public function __construct(SequenceReadStorage $readStorage)
    {
        $this->readStorage = $readStorage;
    }

    public function last(string $sequenceName): ?string
    {
        $sequence = $this->readStorage->get($sequenceName);
        if (null === $sequence) {
            return null;
        }

        return $sequence->getValue();
    }
}