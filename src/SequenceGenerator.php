<?php

namespace Sbooker\PersistentSequences;

use Sbooker\TransactionManager\TransactionManager;

final class SequenceGenerator
{
    private SequenceWriteStorage $storage;

    private TransactionManager $transactionManager;

    public function __construct(
        SequenceWriteStorage $storage,
        TransactionManager $transactionManager
    ) {
        $this->storage = $storage;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws \Throwable
     */
    public function next(string $sequenceName, Algorithm $algorithm): string
    {
        return
            $this->transactionManager->transactional(function () use ($sequenceName, $algorithm): string {
                $sequence = $this->storage->getAndLock($sequenceName);

                if (null === $sequence) {
                    $sequence = new Sequence($sequenceName, $algorithm);
                    $this->storage->add($sequence);
                }

                $sequence->updateValue($algorithm);

                return $sequence->getValue();
            })
        ;
    }
}