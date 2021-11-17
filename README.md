# PHP Object Collections

[![Latest Stable Version](http://poser.pugx.org/wmsamolet/php-object-collections/v)](https://packagist.org/packages/wmsamolet/php-object-collections)
[![Total Downloads](http://poser.pugx.org/wmsamolet/php-object-collections/downloads)](https://packagist.org/packages/wmsamolet/php-object-collections) 
[![Latest Unstable Version](http://poser.pugx.org/wmsamolet/php-object-collections/v/unstable)](https://packagist.org/packages/wmsamolet/php-object-collections) 
[![License](http://poser.pugx.org/wmsamolet/php-object-collections/license)](https://packagist.org/packages/wmsamolet/php-object-collections) 
[![PHP Version Require](http://poser.pugx.org/wmsamolet/php-object-collections/require/php)](https://packagist.org/packages/wmsamolet/php-object-collections)
[![PHP Version Require](https://img.shields.io/badge/Coding%20Style-PSR--12-%23256d4e)](https://www.php-fig.org/psr/psr-12/)

Strongly typed collections for objects and more

### Advantages:
- Storage in collections of **strictly** specified objects
- Ability to specify **ANY [Traversable](https://www.php.net/manual/en/class.traversable.php) object** 
as data (useful for storing ORM Iterators, for example 
[https://www.doctrine-project.org/projects/doctrine-orm/en/2.10/tutorials/pagination.html](https://www.doctrine-project.org/projects/doctrine-orm/en/2.10/tutorials/pagination.html))
- Ability to work with a collection **as an array**
- Possibility of pagination (in batches) using **->batch(...)**, **->slice(...)**, **->page(...)**

## Documentation

- [Usage object static collection](docs/usage-object-static.md)
- [Usage typed static collection](docs/usage-typed-static.md)
- [Testing](docs/testing.md)
- [Contributing](docs/contributing.md)
- [Changelog](docs/changelog.md)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require wmsamolet/php-collections
```

or add

```
"wmsamolet/php-collections": "^1.0"
```

to the requirement section of your `composer.json` file.

## Basic usage **object** collections

```php
<?php

use Wmsamolet\PhpObjectCollections\ObjectCollection;

class TestEntity
{
    /** @var int */
    private $id;
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function setId(int $id): void 
    {
        $this->id = $id;
    }
}

// Create collection with values 1,2,3
$collection = new ObjectCollection(
    TestEntity::class, 
    [
        (new TestEntity())->setId(1), 
        (new TestEntity())->setId(2), 
        (new TestEntity())->setId(3), 
    ]
);

// Print entities: #1,#2,#3
echo '<pre>';
print_r($collection->getList());
echo '</pre>';

// If we try to add another collection to the collection
// WE WILL GET AN EXCEPTION!

class TestOtherEntity extends TestEntity
{
}

$collection->add((new TestOtherEntity())->setId(4));
```

## Basic usage **object static** collections

```php
<?php

use Wmsamolet\PhpObjectCollections\AbstractCollection;

class TestEntity
{
    /** @var int */
    private $id;
    
    public function getId(): int
    {
        return $this->id;
    }
    
    public function setId(int $id): void 
    {
        $this->id = $id;
    }
}

/**
 * Add PhpDoc for IDE autocompletion when working with this collection
 * 
 * @method TestEntity[] getList()
 * @method null|TestEntity get(int $key)
 * @method null|TestEntity getByOffset(int $key)
 */
class ExampleEntityCollection extends AbstractObjectCollection
{
    /**
     * Set collection item as TestEntity object class
     */
    public function collectionObjectClassName(): string
    {
        return TestEntity::class;
    }
}

// Add entities to collection
$collection = new ExampleEntityCollection([
    (new TestEntity())->setId(1),
    (new TestEntity())->setId(2),
    (new TestEntity())->setId(3),
]);

// Print entities: #1,#2,#3
echo '<pre>';
print_r($collection->getList());
echo '</pre>';

// If we try to add another collection or value with another to the collection
// WE WILL GET AN EXCEPTION!

class TestOtherEntity extends TestEntity
{
}

$collection->add((new TestOtherEntity())->setId(4));
$collection->add(5);
```

## Basic usage **typed** collections

```php
<?php

use Wmsamolet\PhpObjectCollections\TypedCollection;

// Create collection with values 1,2,3
$collection = new TypedCollection(TypedCollection::TYPE_INTEGER, [1, 2, 3]);

// Print values: 1,2,3
echo '<pre>';
print_r($collection->getList());
echo '</pre>';

// If we try to add value with another type to the collection
// WE WILL GET AN EXCEPTION!
$collection->add('4');
$collection->add([5]);
```

## Basic usage **typed static** collections

```php
<?php

use Wmsamolet\PhpObjectCollections\AbstractTypedCollection;

/**
 * Add PhpDoc for IDE autocompletion when working with this collection
 * 
 * @method int[] getList()
 * @method null|int get(int $key)
 * @method null|int getByOffset(int $key)
 */
class TestIdCollection extends AbstractTypedCollection
{
    /**
     * Set collection item value type
     */
    public function collectionValueType(): string
    {
        return static::TYPE_INT;
    }
}

// Create collection with ids #1,#2,#3 to collection
$collection = new TestIdCollection([1, 2, 3]);

// Print ids: #1,#2,#3
echo '<pre>';
print_r($collection->getList());
echo '</pre>';

// If we try to add value with another type to the collection
// WE WILL GET AN EXCEPTION!
$collection->add('4');
$collection->add([5]);
```

## License

PHP Object Collections is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
