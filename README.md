PHP Object Collections
====================

Strongly typed collections for objects and more

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require wmsamolet/php-collections
```

or add

```
"wmsamolet/php-collections": "*"
```

to the requirement section of your `composer.json` file.

Usage
-----

### TestEntity
```php
<?php

class TestEntity
{
    /** @var int */
    private $id;
    
    /** @var string */
    private $name;
    
    public  function getId(): int
    {
        return $this->id;
    }
    
    public  function setId(int $id): void 
    {
        $this->id = $id;
    }
    
    public  function getName(): string
    {
        return $this->name;
    }

    public  function setName(string $name): void 
    {
        $this->name = $name;
    }
}
```

### ExampleEntityCollection
```php
<?php

use Wmsamolet\PhpObjectCollections\AbstractCollection;

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
    
    /**
     * [OPTIONAL METHOD]
     * 
     * Collection indexed by entity id
     *  
     * Convert key to entity TestEntity:
     * ->get($key)
     * ->getAll()
     * ->add($value)
     * ->set($key, $value)
     * ->setAll($values) 
     */
    protected function convertKey($key, TestEntity $entity): int 
    {
        return $entiry->getId();
    }
    
    /**
     * [OPTIONAL METHOD]
     * 
     * Allow set item array value
     * 
     * Convert value from array to TestEntity on:
     * ->get($key)
     * ->getAll()
     * ->add($value)
     * ->set($key, $value)
     * ->setAll($values) 
     *
     * @param $value
     * @return TestEntity|mixed
     */
    protected function convertValue($value): TestEntity
    {
        if (is_array($value)) {
            $entity = new TestEntity();
            $entity->setId($value['id']);
            $entity->setName($value['name']);

            $value = $entity;
        }

        return $value;
    }

    /**
     * [OPTIONAL METHOD]
     * 
     * @example (new ExampleEntityCollection([...]))->toArray()
     * 
     * @return string[][]|int[][]
     */  
    public function toArray(): array
    {
        $data = [];

        foreach ($this->getAll() as $key => $entity) {
            $data[$key] = [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
            ];
        }

        return $data;
    }
}
```

### Add entities via __constructor(...)
```php
// Create entity #1
$entity1 = new TestEntity();
$entity1->setId(1);
$entity1->setName('entity_1');

// Add entities #1 to collection
$collection = new ExampleEntityCollection([
    $entity1,
]);
```

### addList
```php
// Create entity #2
$entity2 = new TestEntity();
$entity2->setId(2);
$entity2->setName('entity_2');

// Create entity #3
$entity3 = new TestEntity();
$entity3->setId(3);
$entity3->setName('entity_3');

// Add list entities #2,#3 to collection
$collection->addList([
    $entity2,
    $entity3,
]);
```

### add
```php
// Create entity #4
$entity4 = new TestEntity();
$entity4->setId(4);
$entity4->setName('entity_4');

// Add entity #4 to collection
$collection->add($entity4);

// Add entity #5 to collection as an array
$collection->add([
    'id' => 5,
    'name' => 'entity_5',
]);
```

### getList
```php
// Print entities: #1,#2,#3,#4,#5
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### get
```php
// Print entity #2
echo '<pre>';
print_r($collection->get(2));
echo '</pre>';
```

### getByOffset
```php
// Print entity #4
echo '<pre>';
print_r($collection->getByOffset(3));
echo '</pre>';
```

### remove
```php
// Remove entity #3 from collection
$collection->remove(3);

// Print entities: #1,#2,#4,#5
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### removeAll
```php
// Remove all entities from collection
$collection->removeAll();

// Print empty array
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### toArray
```php
// Get all entities as array data (entities #1,#2,#4,#5)
// Print: array(array('id' => 1, 'name' => 'name_1'), array(...), ...) 
echo '<pre>';
print_r($collection->toArray());
echo '</pre>';
```

### map
```php
/**
 * Map data 
 */
$exampleData = $collection->map(
    function(TestEntity $entity) {
        return $entity->getId();
    },
    function(TestEntity $entity) {
        return $entity->getName();
    }
);

// Print: array(1 => 'name 1', 2 => 'name 2', ...)
echo '<pre>';
print_r($exampleData);
echo '</pre>';
```

### filter
```php
/**
 * Filter collection items
 * Remove entity #2 from collection
 */
$exampleEntityCollection = $collection->filter(
    function(TestEntity $entity) {
        return $entity->getId() !== 2;
    }
);

// Print entities: #1,#3,#4,#5
echo '<pre>';
print_r($exampleEntityCollection->getList());
echo '</pre>';
```

### sort
```php
/**
 * Sort descending by id collection items
 */
$exampleEntityCollection = $collection->sort(
    function(TestEntity $entityA, TestEntity $entityB) {
        return $entityA->getId() < $entityB->getId();
    }
);

// Print entities: #5,#4,#3,#2,#1
echo '<pre>';
print_r($exampleEntityCollection->getList());
echo '</pre>';
```

### batch
```php
/**
 * Batch example
 * 
 * Print: 
 * 
 * array(TestEntity#1, TestEntity#2)
 * array(TestEntity#3, TestEntity#4)
 * array(TestEntity#5)
 * 
 * @var TestEntity[] $exampleEntityBatchList
 */
foreach ($collection->batch(2) as $exampleEntityBatchList) {
    echo '<pre>';
    print_r($exampleEntity);
    echo '</pre>';
}
```
