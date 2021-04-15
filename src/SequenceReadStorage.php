<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences;

interface SequenceReadStorage
{
    public function get(string $sequenceName): ?Sequence;
}