[Read in Russian](README.md)

# Persistent Sequences (`sbooker/persistent-sequences`)

[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Total Downloads][badge-downloads]][downloads]

A concurrency-safe, transactional library for creating and managing named, persistent sequences.

## Library's Purpose

This library is the ideal tool for building **centralized ID generation services** and business number generators.

In complex systems, a standard `AUTO_INCREMENT` is often not enough. `SequenceGenerator` solves the following problems:
*   **Centralized ID Generation:** In distributed systems or when using a DDD approach where an entity's ID must be generated before it is persisted, `SequenceGenerator` can act as a reliable and transactional source of unique IDs.
*   **Named Business Sequences:** Create independent sequences for invoices (`INV-001`), orders (`ORD-002`), etc.
*   **Atomicity:** Guarantees that the next value in a sequence is retrieved along with other operations within a single transaction.
*   **Reliability:** Ensures safe generation in DBMS where `AUTO_INCREMENT` has issues in clustered configurations.

The library is integrated with [sbooker/transaction-manager](https://github.com/sbooker/transaction-manager), which guarantees safety in concurrent environments.

## Key Features

*   **Centralized ID Generation:** Can be used as a single source of truth for generating unique entity identifiers.
*   **Transactional Safety:** Generating a new value occurs within a transaction managed by `transaction-manager`.
*   **Concurrency Safe:** Uses `getLocked()` to prevent data races.
*   **Named Sequences:** An unlimited number of independent sequences.
*   **Flexible Algorithms:** Use the built-in `Increment` or create your own implementation of the `Algorithm` interface.
*   **Separation of Reads and Writes (CQRS):** `SequenceGenerator` for modification and `SequenceReader` for safely reading the current value.

## Installation

```bash
composer require sbooker/persistent-sequences
```
You will also need `sbooker/transaction-manager` and its adapter for your ORM.

## Quick Start

### Step 1: The Goal: Simple ID Retrieval

Your application code for generating **unique IDs or business numbers** will be as simple as possible:

```php
// Generate the next unique ID for a new user
$nextUserId = (int)$sequenceGenerator->next('users_pk', new Algorithm\Increment(1));
$user = new User($nextUserId, /* ... */);
$transactionManager->persist($user);

// Get the next number for an invoice
$nextInvoiceNumber = $sequenceGenerator->next('invoices', new InvoiceAlgorithm());
// $nextInvoiceNumber will be "INV-2023-0001"
```

### Step 2: Assembling Dependencies

`SequenceGenerator` requires a `TransactionManager`.

```php
// bootstrap.php or your DI container

/** @var Sbooker\TransactionManager\TransactionManager $transactionManager */
$sequenceGenerator = new Sbooker\PersistentSequences\SequenceGenerator($transactionManager);
```

### Step 3: Reading the Current Value without Locking (Optional)

If you only need to know the last generated value without incrementing it, use `SequenceReader`.

**1. Implement `SequenceReadStorage`:**
```php
// src/Infrastructure/Persistence/DoctrineSequenceReadStorage.php
use Doctrine\ORM\EntityManagerInterface;
use Sbooker\PersistentSequences\Sequence;
use Sbooker\PersistentSequences\SequenceReadStorage;

final class DoctrineSequenceReadStorage implements SequenceReadStorage { /* ... */ }
```

**2. Assemble and use `SequenceReader`:**
```php
// bootstrap.php or your DI container
/** @var DoctrineSequenceReadStorage $readStorage */
$sequenceReader = new Sbooker\PersistentSequences\SequenceReader($readStorage);

// In your code:
$lastUserId = $sequenceReader->last('users_pk');
```

### Advanced Usage: Custom Algorithm

You can create your own algorithm by implementing the `Algorithm` interface.

```php
// src/Sequences/InvoiceAlgorithm.php
use Sbooker\PersistentSequences\Algorithm;

final class InvoiceAlgorithm implements Algorithm
{
    public function first(): string
    {
        return "INV-2023-0000";
    }

    public function next(string $currentValue): string
    {
        // A simple (but not perfect) implementation for the example
        [$prefix, $year, $number] = explode('-', $currentValue);
        $nextNumber = str_pad((string)((int)$number + 1), 4, '0', STR_PAD_LEFT);

        return "$prefix-$year-$nextNumber";
    }
}

// Usage:
$nextInvoiceNumber = $sequenceGenerator->next('invoices', new InvoiceAlgorithm());
// returns "INV-2023-0001"
```

## License
See [LICENSE][license] file.

[badge-release]: https://img.shields.io/packagist/v/sbooker/persistent-sequences.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/sbooker/persistent-sequences.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/sbooker/persistent-sequences.svg?style=flat-square

[release]: https://packagist.org/packages/sbooker/persistent-sequences
[license]: https://github.com/sbooker/persistent-sequences/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/sbooker/persistent-sequences
