<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences;

interface SequenceWriteStorage
{
    public function add(Sequence $value): void;

    public function getAndLock(string $sequenceName): ?Sequence;
}