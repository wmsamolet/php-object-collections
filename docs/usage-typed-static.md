Usage typed static collections
====================

- [Creating test array typed static collection](#testidcollection)
- [Add entities via __constructor(...)](#add-values-via-__constructor)
- [Collection methods](#methods):
  - [add](#add)
  - [addList](#addlist)
  - [set](#set)
  - [setList](#setlist)
  - [get](#get)
  - [getList](#getlist)
  - [getByOffset](#getbyoffset)
  - [remove](#remove)
  - [removeAll](#removeall)
  - [key](#key)
  - [offset](#offset)
  - [count](#count)
  - [current](#current)
  - [first](#first)
  - [firstKey](#firstkey)
  - [last](#last)
  - [lastKey](#lastkey)
  - [map](#map)
  - [filter](#filter)
  - [sort](#sort)
  - [batch](#batch)
  - [batchCount](#batchcount)
  - [slice](#slice)
  - [page](#page)
  - [pageCount](#pagecount)
  - [setIterator](#setiterator)

### TestIdCollection
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
```

### Add values via __constructor(...)
```php
// Create collection with ids #1,#2,#3 to collection
$collection = new TestIdCollection([1, 2, 3]);
```

## Methods:

### add
```php
// Create collection with ids #1,#2,#3 to collection
$collection = new TestIdCollection([1, 2, 3]);

// Add id #4 to collection
$collection->add(4);
```

### addList
```php
// Create collection with id #1
$collection = new TestIdCollection([1]);

// Add list entities #2,#3 to collection
$collection->addList([2, 3]);

// Add list entities #4,#5,#6 to collection
$collection->addList([4, 5, 6]);

```

### set
```php
// Create collection with ids #1,#2,#3 to collection
$collection = new TestIdCollection([1, 2, 3]);

// Replace id #2 on id #4
$collection->set(1, 4);

// Replace id #3 on id #4
$collection->set(2, 5);
```

### setList
```php
// Create collection with ids #1,#2,#3 to collection
$collection = new TestIdCollection([1, 2, 3]);

// Replace id #1 on id #4
// Replace id #2 on id #5
$collection->setList([0 => 4, 1 => 5]);
```

### get
```php
$collection = new TestIdCollection();

foreach (range(5, 50) as $id) {
    $collection->add($id);
}

// Print
echo '<pre>';
print_r($collection->get(2));
echo '</pre>';
```

### getList
```php
$collection = new TestIdCollection();

for ($id = 0; $id <= 5; $id++) {
    $collection->add($id);
}

// Print ids: #1,#2,#3,#4,#5
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### getByOffset
```php
$collection = new TestIdCollection();

for ($id = 1; $id <= 5; $id++) {
    $collection->add($id);
}

// Print id #4
echo '<pre>';
print_r($collection->getByOffset(3));
echo '</pre>';
```

### remove
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Remove id #4 from collection
$collection->remove(3);

// Print ids: #1,#2,#3,#5
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### removeAll
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Remove all ids from collection
$collection->removeAll();

// Print empty array
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### key
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print id #1
echo '<pre>';
print_r($collection->key());
echo '</pre>';

$collection->next();

// Print id  #2
echo '<pre>';
print_r($collection->key());
echo '</pre>';
```

### offset
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print id #3
echo '<pre>';
print_r($collection->offset(2));
echo '</pre>';
```

### count
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print 5
echo '<pre>';
print_r($collection->count());
echo '</pre>';
```

### current
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print id #1
echo '<pre>';
print_r($collection->current());
echo '</pre>';

$collection->next();

// Print id #2
echo '<pre>';
print_r($collection->current());
echo '</pre>';
```

### first
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print id #1
echo '<pre>';
print_r($collection->first());
echo '</pre>';
```

### firstKey
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print 0
echo '<pre>';
print_r($collection->firstKey());
echo '</pre>';
```

### last
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print last id #5
echo '<pre>';
print_r($collection->last());
echo '</pre>';
```

### lastKey
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print 4
echo '<pre>';
print_r($collection->lastKey());
echo '</pre>';
```

### map
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

/**
 * Map data 
 */
$exampleData = $collection->map(
    function(int $id) {
        return $id;
    },
    function(int $id, TestIdCollection $that) {
        return ($that->key() + 1) * 10;
    }
);

// Print: array(1 => 10, 2 => 20, 3 => 30, 4 => 40, 5 => 50)
echo '<pre>';
print_r($exampleData);
echo '</pre>';
```

### filter
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

/**
 * Filter collection items
 * Remove entity #2 from collection
 */
$collection->filter(
    function(int $id, TestIdCollection $that) {
        return $id !== 2 && $that->key() !== 3;
    }
);

// Print ids: #1,#3,#5
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### sort
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

/**
 * Sort descending by id collection items
 */
$collection->sort(
    function(int $idA, int $idB) {
        return $idA < $idB;
    }
);

// Print ids: #5,#4,#3,#2,#1
echo '<pre>';
print_r($collection->getList());
echo '</pre>';
```

### batch
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

/**
 * Batch example
 * 
 * Print: 
 * 
 * array(id#1, id#2)
 * array(id#3, id#4)
 * array(id#5)
 * 
 * @var TestEntity[] $exampleEntityBatchList
 */
foreach ($collection->batch(2) as $exampleEntityBatchList) {
    echo '<pre>';
    print_r($exampleEntity);
    echo '</pre>';
}
```

### batchCount
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print 3
echo '<pre>';
print_r($collection->batchCount(2));
echo '</pre>';
```

### slice
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print ids #2,#3,#4
echo '<pre>';
print_r($collection->slice(1, 3));
echo '</pre>';
```

### page
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print ids #3,#4
echo '<pre>';
print_r($collection->page(2, 2));
echo '</pre>';
```

### pageCount
Alias for bachCount($size)
```php
// Create collection with ids #1,#2,#3,#4,#5 to collection
$collection = new TestIdCollection([1, 2, 3, 4, 5]);

// Print 3
echo '<pre>';
print_r($collection->pageCount(2));
echo '</pre>';
```

### setIterator
```php
// Set ArrayIterator with entities
$collection = (new TestIdCollection())
    ->setIterator(
        new ArrayIterator([1, 2])
    );
```
