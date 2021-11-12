Usage
====================

- [Creating test entity](#testentity)
- [Creating test collection](#testobjectcollection)
- [Collection methods](#methods):
  - [Add entities via __constructor(...)](#add-entities-via-__constructor)
  - [add](#add)
  - [addList](#addlist)
  - [set](#set)
  - [setList](#setlist)
  - [get](#get)
  - [getList](#getlist)
  - [getByOffset](#getbyoffset)
  - [remove](#remove)
  - [removeAll](#removeall)
  - [map](#map)
  - [filter](#filter)
  - [sort](#sort)
  - [batch](#batch)
  - [toArray](#toarray)

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

### TestObjectCollection
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
class TestObjectCollection extends AbstractObjectCollection
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
     * @example (new TestObjectCollection([...]))->toArray()
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

## Methods:

### Add entities via __constructor(...)
```php
// Create entity #1
$entity1 = new TestEntity();
$entity1->setId(1);
$entity1->setName('entity_1');

// Add entities #1 to collection
$collection = new TestObjectCollection([
    $entity1,
]);
```

### add
```php
// Create entity #1
$entity1 = (new TestEntity())
    ->setId(1)
    ->setName('entity_1');

// Add entity #1 to collection
$collection->add($entity1);

// Or add as array data
$collection->add([
    'id' => $entity1->getId(),
    'name' => $entity1->getName(),
]);
```

### addList
```php
// Create entity #1
$entity1 = (new TestEntity())
    ->setId(1)
    ->setName('entity_1');

// Create entity #2
$entity2 = (new TestEntity())
    ->setId(2)
    ->setName('entity_2');

// Add list entities #1,#2 to collection
$collection->addList([
    $entity1,
    $entity2,
]);
```

### set
```php
// Create entity #1
$entity1 = (new TestEntity())
    ->setId(1)
    ->setName('entity_1');

// Add entity #1 to collection (automatically set key from id)
$collection->set(null, $entity1);

// Set entity #1 to collection (replace)
$collection->set($entity1->getId(), $entity1);

// Or set as array data (replace)
$collection->set($entity1->getId(), [
    'id' => $entity1->getId(),
    'name' => $entity1->getName(),
]);
```

### setList
```php
// Create entity #1
$entity1 = (new TestEntity())
    ->setId(1)
    ->setName('entity_1');
    
// Create entity #2
$entity2 = (new TestEntity())
    ->setId(2)
    ->setName('entity_2');

// Add entity #1 and #2 to collection
$collection->setList([
    $entity1->getId() => $entity1,
    $entity2->getId() => $entity2,
]);

// Or set all as array data
$collection->set([
    [
        'id' => $entity1->getId(),
        'name' => $entity1->getName(),
    ],
    [
        'id' => $entity2->getId(),
        'name' => $entity2->getName(),
    ],
]);
```

### get
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

// Print entity #2
echo '<pre>';
print_r($collection->get(2));
echo '</pre>';
```

### getList
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

// Print entities: #1,#2,#3,#4,#5
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### getByOffset
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

// Print entity #4
echo '<pre>';
print_r($collection->getByOffset(3));
echo '</pre>';
```

### remove
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

// Remove entity #3 from collection
$collection->remove(3);

// Print entities: #1,#2,#4,#5
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### removeAll
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

// Remove all entities from collection
$collection->removeAll();

// Print empty array
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### map
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

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
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

/**
 * Filter collection items
 * Remove entity #2 from collection
 */
$TestObjectCollection = $collection->filter(
    function(TestEntity $entity) {
        return $entity->getId() !== 2;
    }
);

// Print entities: #1,#3,#4,#5
echo '<pre>';
print_r($TestObjectCollection->getList());
echo '</pre>';
```

### sort
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

/**
 * Sort descending by id collection items
 */
$TestObjectCollection = $collection->sort(
    function(TestEntity $entityA, TestEntity $entityB) {
        return $entityA->getId() < $entityB->getId();
    }
);

// Print entities: #5,#4,#3,#2,#1
echo '<pre>';
print_r($TestObjectCollection->getList());
echo '</pre>';
```

### batch
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

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

### toArray
```php
$collection = new TestCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add(
        (new TestEntity())
            ->setId($id)
            ->setName('entity_' . $id);
    );
}

// Get all entities as array data (entities #1,#2,#4,#5)
// Print: array(array('id' => 1, 'name' => 'name_1'), array(...), ...) 
echo '<pre>';
print_r($collection->toArray());
echo '</pre>';
```