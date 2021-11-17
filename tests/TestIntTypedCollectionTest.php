<?php

namespace Wmsamolet\PhpObjectCollections\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionValidateException;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestEntity;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestIntTypedCollection;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestOtherEntity;

final class TestIntTypedCollectionTest extends TestCase
{
    private const COLLECTION_SIZE = 5;

    /**
     * @return int[]
     */
    public function generateValueList(): array
    {
        $generatedValueList = [];

        for ($i = 0; $i < self::COLLECTION_SIZE; $i++) {
            /** @noinspection RandomApiMigrationInspection */
            $generatedValueList[] = rand(0, 999);
        }

        return $generatedValueList;
    }

    public function testCreationThroughTheConstructor(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        $this->assertCollection($collection, $generatedValueList);
    }

    public function testAdd(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection();

        foreach ($generatedValueList as $entity) {
            $collection->add($entity);
        }

        $this->assertCollection($collection, $generatedValueList);
    }

    public function testAddList(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = (new TestIntTypedCollection())->addList($generatedValueList);

        $this->assertCollection($collection, $generatedValueList);
    }

    public function testSet(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection();

        foreach ($generatedValueList as $generatedValue) {
            $collection->set(true, $generatedValue);
        }

        $this->assertCollection($collection, $generatedValueList);
    }

    public function testSetList(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = (new TestIntTypedCollection())->setList($generatedValueList);

        $this->assertCollection($collection, $generatedValueList);
    }

    public function testSetIterator(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = (new TestIntTypedCollection())
            ->setIterator(
                new ArrayIterator($generatedValueList)
            );

        $this->assertCollection($collection, $generatedValueList);
    }

    public function testFirstKey(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        $this->assertSame($collection->firstKey(), 0);
    }

    public function testFirstValue(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        $this->assertSame(
            $collection->first(),
            $generatedValueList[0]
        );
    }

    public function testLastKey(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        $this->assertSame(
            $collection->lastKey(),
            self::COLLECTION_SIZE - 1
        );
    }

    public function testLastValue(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        $this->assertSame(
            $collection->last(),
            $generatedValueList[self::COLLECTION_SIZE - 1]
        );
    }

    public function testHas(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        foreach (array_keys($generatedValueList) as $key) {
            $this->assertTrue($collection->has($key));
        }
    }

    public function testGetList(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        $collectionValueList = $collection->getList();

        foreach ($generatedValueList as $offset => $generatedValue) {
            $collectionValue = $collectionValueList[$offset] ?? null;

            $this->assertNotEmpty($collectionValue);

            if ($collectionValue !== null) {
                $this->assertSame($collectionValue, $generatedValue);
            }
        }
    }

    public function testGetAllKeys(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        $diff = array_diff(
            array_keys($generatedValueList),
            $collection->keyList()
        );

        $this->assertCount(0, $diff);
    }

    public function testGetByOffset(): void
    {
        $generatedValueList = [];
        $generatedValueListTemp = $this->generateValueList();
        $generatedValueListTempKeys = array_keys($generatedValueListTemp);

        shuffle($generatedValueListTempKeys);

        foreach ($generatedValueListTempKeys as $offset) {
            $generatedValueList[$offset] = $generatedValueListTemp[$offset];

            unset($generatedValueListTemp[$offset]);
        }

        $collection = new TestIntTypedCollection($generatedValueList);

        $offset = 0;

        foreach ($generatedValueList as $generatedValue) {
            $collectionValue = $collection->getByOffset($offset);

            $this->assertSame($collectionValue, $generatedValue);

            $offset++;
        }
    }

    public function testRemove(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        /** @noinspection PhpAssignmentInConditionInspection */
        while ($generatedValue = array_shift($generatedValueList)) {
            $key = $collection->firstKey();

            $collectionValue = $collection->get($key);

            $collection->remove($key);

            $this->assertSame(count($generatedValueList), $collection->count());
            $this->assertSame($generatedValue, $collectionValue);
        }

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);

        $generatedValueList = $this->generateValueList();

        foreach ($generatedValueList as $generatedValue) {
            $collection->add($generatedValue);
        }

        $this->assertSame(count($generatedValueList), $collection->count());
        $this->assertCount(count($generatedValueList), $collection->getList());

        /** @noinspection PhpAssignmentInConditionInspection */
        while ($generatedValue = array_pop($generatedValueList)) {
            $key = $collection->lastKey();
            $collectionValue = $collection->get($key);

            $collection->remove($key);

            $this->assertSame(count($generatedValueList), $collection->count());
            $this->assertSame($generatedValue, $collectionValue);
        }

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);
    }

    public function testRemoveAll(): void
    {
        $generatedValueList = $this->generateValueList();

        $collection = new TestIntTypedCollection($generatedValueList);

        $this->assertCount(count($generatedValueList), $collection->getList());
        /** @noinspection PhpUnitTestsInspection */
        $this->assertSame($collection->count(), count($generatedValueList));

        $collection->removeAll();

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);

        foreach ($generatedValueList as $generatedEntity) {
            $collection->add($generatedEntity);
        }

        $this->assertCount(count($generatedValueList), $collection->getList());
        /** @noinspection PhpUnitTestsInspection */
        $this->assertSame(count($generatedValueList), $collection->count());

        $collection->removeAll();

        $this->assertCount(0, $collection->getList());
        $this->assertSame(0, $collection->count());
    }

    public function testMap(): void
    {
        $generatedValueList = $this->generateValueList();

        $collection = new TestIntTypedCollection($generatedValueList);

        $mapData = $collection->map(
            function ($value) {
                return $value;
            }
        );

        foreach ($generatedValueList as $offset => $generatedValue) {
            $this->assertArrayHasKey($offset, $mapData);
            $this->assertSame($generatedValue, $mapData[$offset]);
        }

        $mapData = $collection->map(
            function ($value, $that) {
                return $that->key();
            },
            function (int $value) {
                return $value * 10;
            }
        );

        foreach ($generatedValueList as $generatedKey => $generatedValue) {
            $this->assertArrayHasKey($generatedKey, $mapData);
            $this->assertSame($generatedValue * 10, $mapData[$generatedKey]);
        }
    }

    public function testFilter(): void
    {
        $generatedValueList = $this->generateValueList();
        $collection = new TestIntTypedCollection($generatedValueList);

        $generatedValueList = array_values(
            array_filter(
                $generatedValueList,
                static function ($value) {
                    return $value > 500;
                }
            )
        );

        $collection->filter(
            function ($value) {
                return $value > 500;
            }
        );

        $this->assertCollection($collection, $generatedValueList);
    }

    public function testSort(): void
    {
        $generatedValueList = $this->generateValueList();

        $collection = new TestIntTypedCollection($generatedValueList);

        usort(
            $generatedValueList,
            static function ($valueA, $valueB) {
                return $valueA < $valueB;
            }
        );

        $collection->sort(
            static function ($valueA, $valueB) {
                return $valueA < $valueB;
            }
        );

        $this->assertCollection($collection, $generatedValueList);
    }

    public function testBatch(): void
    {
        $generatedValueList = $this->generateValueList();

        $collection = new TestIntTypedCollection($generatedValueList);

        $batchSize = 2;
        $collectionEntityListTotalCount = 0;

        foreach ($collection->batch($batchSize) as $collectionEntityList) {
            $collectionEntityListCount = count($collectionEntityList);

            $this->assertLessThanOrEqual($batchSize, $collectionEntityListCount);

            $collectionEntityListTotalCount += $collectionEntityListCount;
        }

        $this->assertCount($collectionEntityListTotalCount, $generatedValueList);
    }

    public function testBatchCallback(): void
    {
        $generatedValueList = $this->generateValueList();

        /** @noinspection RandomApiMigrationInspection */
        $newValue = rand(1000, 2000);

        $collection = new TestIntTypedCollection($generatedValueList);
        $collection->setBatchCallback(
            function ($size, callable $defaultCallback, TestIntTypedCollection $that) use ($newValue) {
                $that->add($newValue);

                return $defaultCallback($size);
            }
        );

        $generatedValueList[] = $newValue;

        $batchSize = 2;
        $collectionEntityListTotalCount = 0;

        foreach ($collection->batch($batchSize) as $collectionEntityList) {
            $collectionEntityListCount = count($collectionEntityList);

            $this->assertLessThanOrEqual($batchSize, $collectionEntityListCount);

            $collectionEntityListTotalCount += $collectionEntityListCount;
        }

        $this->assertCount($collectionEntityListTotalCount, $generatedValueList);
    }

    public function testBatchCount(): void
    {
        $generatedValueList = $this->generateValueList();

        $collection = new TestIntTypedCollection($generatedValueList);

        $batchSize = 2;
        $batchCount = (int)ceil(count($generatedValueList) / $batchSize);

        $collectionBatchCount = $collection->batchCount($batchSize);

        $this->assertSame($collectionBatchCount, $batchCount);
    }

    public function testSlice(): void
    {
        $generatedValueList = $this->generateValueList();

        $sliceOffset = 1;
        $sliceLength = count($generatedValueList) - 2;

        $generatedEntitySliceList = array_slice($generatedValueList, $sliceOffset, $sliceLength);

        $this->assertLessThanOrEqual($sliceLength, count($generatedEntitySliceList));

        $collection = new TestIntTypedCollection($generatedValueList);
        $collectionValueSliceList = $collection->slice($sliceOffset, $sliceLength);

        $this->assertLessThanOrEqual($sliceLength, count($collectionValueSliceList));

        foreach ($generatedEntitySliceList as $i => $generatedValueSlice) {
            $this->assertNotEmpty($collectionValueSliceList[$i]);
            $this->assertSame($generatedValueSlice, $collectionValueSliceList[$i]);
        }
    }

    public function testPage(): void
    {
        $generatedValueList = $this->generateValueList();

        $collection = new TestIntTypedCollection($generatedValueList);

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedValueList) / $pageLimit);

        $totalCollectionItemsCount = 0;

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $pageItems = $collection->page($pageNumber, $pageLimit);

            $this->assertLessThanOrEqual($pageLimit, count($pageItems));

            $totalCollectionItemsCount += count($pageItems);
        }

        $this->assertCount($totalCollectionItemsCount, $generatedValueList);
    }

    public function testPageCallback(): void
    {
        $generatedValueList = $this->generateValueList();

        /** @noinspection RandomApiMigrationInspection */
        $newValue = rand(1000, 1999);

        $collection = new TestIntTypedCollection($generatedValueList);
        $collection->setPageCallback(
            function (
                $number,
                $limit,
                $preserveKeys,
                callable $defaultCallback,
                TestIntTypedCollection $that
            ) use (
                $newValue
            ) {
                $that->add($newValue);

                return $defaultCallback($number, $limit, $preserveKeys);
            }
        );

        $generatedValueList[] = $newValue;

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedValueList) / $pageLimit);

        $totalCollectionItemsCount = 0;

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $pageItems = $collection->page($pageNumber, $pageLimit);

            $this->assertLessThanOrEqual($pageLimit, count($pageItems));

            $totalCollectionItemsCount += count($pageItems);
        }

        $this->assertCount($totalCollectionItemsCount, $generatedValueList);
    }

    public function testPageCount(): void
    {
        $generatedValueList = $this->generateValueList();

        $collection = new TestIntTypedCollection($generatedValueList);

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedValueList) / $pageLimit);

        $collectionPageCount = $collection->pageCount($pageLimit);

        $this->assertSame($collectionPageCount, $pageCount);
    }

    public function testSetInvalidItemValue(): void
    {
        $this->expectException(CollectionValidateException::class);

        (new TestIntTypedCollection())->set(0, '1');
    }

    protected function assertCollection(
        TestIntTypedCollection $collection,
        array $valueList = null
    ): void {
        $valueList = $valueList ?? $this->generateValueList();

        $this->assertCount($collection->count(), $valueList);

        foreach ($valueList as $offset => $value) {
            $collectionValue = $collection->getByOffset($offset);

            $this->assertNotNull(
                $collectionValue,
                '$collectionValue is NULL'
            );

            if ($collectionValue !== null) {
                $this->assertSame($collectionValue, $value);
            }
        }
    }
}
