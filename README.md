# PHP Object Collections

Strongly typed collections for objects and more

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

## Documentation

- [Usage Instructions](docs/usage.md)

## Testing

```
composer phpcs
composer test
```

or via docker-compose

```
docker-composer run php72 composer phpcs
docker-composer run php72 composer test
```

```
docker-composer run php73 composer phpcs
docker-composer run php73 composer test
```

```
docker-composer run php74 composer phpcs
docker-composer run php74 composer test
```

```
docker-composer run php80 composer phpcs
docker-composer run php80 composer test
```

## License

Monolog is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
