[Read in English](README.EN.md)

# Persistent Sequences (`sbooker/persistent-sequences`)

[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Total Downloads][badge-downloads]][downloads]

Конкурентно-безопасная, транзакционная библиотека для создания и управления именованными персистентными последовательностями.

## Назначение библиотеки

Эта библиотека является идеальным инструментом для **построения единых центров генерации идентификаторов (ID)** и бизнес-номеров.

В сложных системах стандартного `AUTO_INCREMENT` бывает недостаточно. `SequenceGenerator` решает следующие задачи:
*   **Централизованная генерация ID:** В распределенных системах или при использовании подхода DDD, где ID сущности должен быть сгенерирован до ее сохранения, `SequenceGenerator` может выступать как надежный и транзакционный источник уникальных ID.
*   **Именованные бизнес-последовательности:** Создание независимых последовательностей для счетов (`INV-001`), заказов (`ORD-002`) и т.д.
*   **Атомарность:** Гарантированное получение следующего значения в последовательности вместе с другими операциями в рамках одной транзакции.
*   **Надежность:** Безопасная генерация в СУБД, где `AUTO_INCREMENT` имеет проблемы в кластерных конфигурациях.

Библиотека интегрирована с [sbooker/transaction-manager](https://github.com/sbooker/transaction-manager), что гарантирует безопасность при одновременном доступе.

## Ключевые особенности

*   **Централизованная генерация ID:** Может использоваться как единый источник правды для генерации уникальных идентификаторов сущностей.
*   **Транзакционная безопасность:** Генерация нового значения происходит внутри транзакции, управляемой `transaction-manager`.
*   **Безопасность при конкурентном доступе:** Использует `getLocked()` для предотвращения гонок данных.
*   **Именованные последовательности:** Неограниченное количество независимых последовательностей.
*   **Гибкие алгоритмы:** Используйте встроенный `Increment` или создайте собственную реализацию интерфейса `Algorithm`.
*   **Разделение чтения и записи (CQRS):** `SequenceGenerator` для изменения и `SequenceReader` для безопасного чтения текущего значения.

## Установка

```bash
composer require sbooker/persistent-sequences
```
Вам также понадобится `sbooker/transaction-manager` и его адаптер для вашей ORM.

## Быстрый старт

### Шаг 1: Конечная цель — простое получение ID

Ваш прикладной код для генерации **уникальных ID или бизнес-номеров** будет выглядеть максимально просто:

```php
// Генерируем следующий уникальный ID для нового пользователя
$nextUserId = (int)$sequenceGenerator->next('users_pk', new Algorithm\Increment(1));
$user = new User($nextUserId, /* ... */);
$transactionManager->persist($user);

// Получаем следующий номер для счета
$nextInvoiceNumber = $sequenceGenerator->next('invoices', new InvoiceAlgorithm());
// $nextInvoiceNumber будет "INV-2023-0001"
```

### Шаг 2: Сборка зависимостей

Для работы `SequenceGenerator` требуется `TransactionManager`.

```php
// bootstrap.php или ваш DI-контейнер

/** @var Sbooker\TransactionManager\TransactionManager $transactionManager */
$sequenceGenerator = new Sbooker\PersistentSequences\SequenceGenerator($transactionManager);
```

### Шаг 3: Чтение текущего значения без блокировки (опционально)

Если вам нужно просто узнать последнее сгенерированное значение, не увеличивая его, используйте `SequenceReader`.

**1. Реализуйте `SequenceReadStorage`:**
```php
// src/Infrastructure/Persistence/DoctrineSequenceReadStorage.php
use Doctrine\ORM\EntityManagerInterface;
use Sbooker\PersistentSequences\Sequence;
use Sbooker\PersistentSequences\SequenceReadStorage;

final class DoctrineSequenceReadStorage implements SequenceReadStorage { /* ... */ }
```

**2. Соберите и используйте `SequenceReader`:**
```php
// bootstrap.php или ваш DI-контейнер
/** @var DoctrineSequenceReadStorage $readStorage */
$sequenceReader = new Sbooker\PersistentSequences\SequenceReader($readStorage);

// В вашем коде:
$lastUserId = $sequenceReader->last('users_pk');
```

### Продвинутое использование: собственный алгоритм

Вы можете легко создать свой алгоритм, реализовав интерфейс `Algorithm`.

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
        // Простая (но не идеальная) реализация для примера
        [$prefix, $year, $number] = explode('-', $currentValue);
        $nextNumber = str_pad((string)((int)$number + 1), 4, '0', STR_PAD_LEFT);

        return "$prefix-$year-$nextNumber";
    }
}

// Использование:
$nextInvoiceNumber = $sequenceGenerator->next('invoices', new InvoiceAlgorithm());
// вернет "INV-2023-0001"
```

## License
See [LICENSE][license] file.

[badge-release]: https://img.shields.io/packagist/v/sbooker/persistent-sequences.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/sbooker/persistent-sequences.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/sbooker/persistent-sequences.svg?style=flat-square

[release]: https://img.shields.io/packagist/v/sbooker/persistent-sequences
[license]: https://github.com/sbooker/persistent-sequences/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/sbooker/persistent-sequences
