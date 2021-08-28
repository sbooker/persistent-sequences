<?php

namespace Sbooker\PersistentSequences;

use Sbooker\TransactionManager\TransactionManager;

final class SequenceGenerator
{
    private TransactionManager $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws \Throwable
     */
    public function next(string $sequenceName, Algorithm $algorithm): string
    {
        return
            $this->transactionManager->transactional(function () use ($sequenceName, $algorithm): string {
                $sequence = $this->transactionManager->getLocked(Sequence::class, $sequenceName);

                if (null === $sequence) {
                    $sequence = new Sequence($sequenceName, $algorithm);
                    $this->transactionManager->persist($sequence);
                }

                $sequence->updateValue($algorithm);

                return $sequence->getValue();
            })
        ;
    }
}