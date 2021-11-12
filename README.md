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
- Possibility of pagination (in batches) using **->batch($size)**

## Documentation

- [Usage Instructions](docs/usage.md)
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

## Basic usage

### TestEntity
```php
<?php

use Wmsamolet\PhpObjectCollections\AbstractCollection;

class TestEntity
{
    /** @var int */
    private $id;
    
    public  function getId(): int
    {
        return $this->id;
    }
    
    public  function setId(int $id): void 
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
```

## License

PHP Object Collections is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
