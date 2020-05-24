# Persistent sequences

[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Total Downloads][badge-downloads]][downloads]

Supports custom number generation by user-defined algorithms. 

## Installation
```bash
composer require sbooker/persistent-sequences 
```

## Usage

Step 1. Define Sequence mapping to persistent storage

Step 2. Define you custom sequence calculation algorithm. For example:
```php
class ConcreteAlgorithm implements \Sbooker\PersistentSequences\Algorithm
{
    public function first(): string
    {
        // return first item in sequence
    }
    
    public function next(string $currentValue): string 
    {
        // return next item in sequence
    }
}
```
Step 3. Define you sequence write storage or use Doctrine implementation
```php
class ConcreteWriteStorage implements \Sbooker\PersistentSequences\SequenceWriteStorage
{
    //...
}
```
Step 4. Configure SequenceGenerator
```php
$sequenceGenerator =
    new \Sbooker\PersistentSequences\SequenceGenerator(
        new ConcreteWriteStorage(),
        new \Sbooker\TransactionManager\TransactionManager(
            // see sbooker/transaction-manager
        )   
    );
```
Step 5. Use SequenceGenerator to generate sequence
```php
$number = $sequenceGenerator->next('concrete-seq', new ConcreteAlgorithm());
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
