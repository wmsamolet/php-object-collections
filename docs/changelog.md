# Changelog:
[Semantic Versioning](https://semver.org/)

### v1.8.0
- Improved documentation
- Renamed "formatted" to "converted"
- Added ::fromArray(array $items) static fabric method for **_Wmsamolet\PhpObjectCollections\AbstractCollection_**

### v1.7.1
- Improved documentation
- Fix remove offsetKey(...) return type

### v1.7.0
- Fix ->setDataArrayIterator() **_Wmsamolet\PhpObjectCollections\ArrayIteratorIterator_**
- Added ->loadData(...) method for **_Wmsamolet\PhpObjectCollections\ArrayIteratorIterator_**
- Set ->processData(...) in **_Wmsamolet\PhpObjectCollections\ArrayIteratorIterator_** as deprecated

### v1.6.0
- Fixed a mutability bug when cloning a collection
- Added __clone() method for **_Wmsamolet\PhpObjectCollections\AbstractCollection_**
- Added ->copy() method for **_Wmsamolet\PhpObjectCollections\AbstractCollection_**
- Added ->getDataArrayIterator() method for **_Wmsamolet\PhpObjectCollections\ArrayIteratorIterator_**
- Added ->setDataArrayIterator(...) method for **_Wmsamolet\PhpObjectCollections\ArrayIteratorIterator_**

### v1.5.1
- Fix ->validate() exceptions

### v1.5.0
- Added ->getConvertKeyCallback() method for **_Wmsamolet\PhpObjectCollections\ObjectCollection_**
- Added ->setConvertKeyCallback(...) method for **_Wmsamolet\PhpObjectCollections\ObjectCollection_**
- Added ->getConvertKeyCallback() method for **_Wmsamolet\PhpObjectCollections\TypedCollection_**
- Added ->setConvertKeyCallback(...) method for **_Wmsamolet\PhpObjectCollections\TypedCollection_**
- Added $convertValueCallback __constructor() argument for **_Wmsamolet\PhpObjectCollections\TypedCollection_**
- Added ::fromIterator(Traversable $iterator) static fabric method for **_Wmsamolet\PhpObjectCollections\AbstractCollection_**
 
### v1.4.0
- Added collectionKeyType() method for **_Wmsamolet\PhpObjectCollections\AbstractTypedCollection_**

### v1.3.0
- Improved documentation
- Improved tests
- Added a class for creating dynamic object collections: **_Wmsamolet\PhpObjectCollections\ObjectCollection_**
- Added a class for creating typed collections: **_Wmsamolet\PhpObjectCollections\TypedCollection_**
- Added abstract class for declaring typed static collections: **_Wmsamolet\PhpObjectCollections\AbstractTypedCollection_**
- Added composer command for validate coding standards and run all test: ```composer check```

### v1.2.3
- Improved documentation

### v1.2.2
- Improved documentation

### v1.2.1
- Improved documentation

### v1.2.0
- Improved documentation
- Added method ->setBatchCallback($callback)
- Added method ->getBatchCallback()
- Added method ->setPageCallback($callback)
- Added method ->getPageCallback()
- Added tests for new methods

### v1.1.1
- Improved documentation

### v1.1.0
- Improved documentation
- Added method ->batchCount($size)
- Added method ->slice($offset, $length)
- Added method ->page($number, $limit)
- Added method ->pageCount($limit)
- Added tests for new methods

### v1.0.4
- Improved documentation
- Add keywords to composer.json

### v1.0.3
- Improved documentation
- Fix license

### v1.0.2
- Improved documentation
- Remove constructor from Wmsamolet\PhpObjectCollections\ArrayIteratorIterator

### v1.0.1
- Improved documentation
- Fixed inconsistencies with PSR-12 code standards

### v1.0.0
- Release