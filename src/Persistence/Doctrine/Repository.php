<?php

declare(strict_types=1);

namespace Sbooker\PersistentSequences\Persistence\Doctrine;

use Sbooker\PersistentSequences\Sequence;
use Sbooker\PersistentSequences\SequenceWriteStorage;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;

final class Repository extends EntityRepository implements SequenceWriteStorage
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
}