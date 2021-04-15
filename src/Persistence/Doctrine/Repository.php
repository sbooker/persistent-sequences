<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences\Persistence\Doctrine;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Sbooker\PersistentSequences\Sequence;
use Sbooker\PersistentSequences\SequenceReadStorage;
use Sbooker\PersistentSequences\SequenceWriteStorage;

final class Repository extends EntityRepository implements SequenceWriteStorage, SequenceReadStorage
{
    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(Sequence $value): void
    {
        $this->getEntityManager()->persist($value);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getAndLock(string $sequenceName): ?Sequence
    {
        return $this->find($sequenceName, LockMode::PESSIMISTIC_WRITE);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $sequenceName): ?Sequence
    {
        return $this->find($sequenceName);
    }
}