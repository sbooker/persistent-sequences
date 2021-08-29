<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences\Persistence\Doctrine;

use Doctrine\ORM\EntityRepository;
use Sbooker\PersistentSequences\Sequence;
use Sbooker\PersistentSequences\SequenceReadStorage;

final class Repository extends EntityRepository implements SequenceReadStorage
{
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $sequenceName): ?Sequence
    {
        return $this->find($sequenceName);
    }
}