<?php

namespace Wmsamolet\PhpObjectCollections\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionValidateException;
use Wmsamolet\PhpObjectCollections\ObjectCollection;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestEntity;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestOtherEntity;

final class ObjectCollectionTest extends TestCase
{
    private const COLLECTION_SIZE = 5;

    /**
     * @return TestEntity[]
     */
    public function generateObjectList(): array
    {
        $entityList = [];
        $idList = range(1, self::COLLECTION_SIZE);

        foreach ($idList as $id) {
            $entityList[] = (new TestEntity())
                ->setId($id)
                ->setName('Name ' . $id)
                ->setTitle('Title ' . $id);
        }

        return $entityList;
    }

    public function testCreationThroughTheConstructor(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $this->assertCollection($collection, $generatedObjectList);
    }

    public function testAdd(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class);

        foreach ($generatedObjectList as $entity) {
            $collection->add($entity);
        }

        $this->assertCollection($collection, $generatedObjectList);
    }

    public function testAddList(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = (new ObjectCollection(TestEntity::class))
            ->addList($generatedObjectList);

        $this->assertCollection($collection, $generatedObjectList);
    }

    public function testSet(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class);

        foreach ($generatedObjectList as $entity) {
            $collection->set($entity->getId(), $entity);
        }

        $this->assertCollection($collection, $generatedObjectList);
    }

    public function testSetList(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = (new ObjectCollection(TestEntity::class))
            ->setList($generatedObjectList);

        $this->assertCollection($collection, $generatedObjectList);
    }

    public function testSetIterator(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = (new ObjectCollection(TestEntity::class))
            ->setIterator(
                new ArrayIterator($generatedObjectList)
            );

        $this->assertCollection($collection, $generatedObjectList);
    }

    public function testFirstKey(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $this->assertSame($collection->firstKey(), 0);
    }

    public function testFirstValue(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $this->assertSame(
            $collection->first(),
            $generatedObjectList[0]
        );
    }

    public function testLastKey(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $this->assertSame(
            $collection->lastKey(),
            self::COLLECTION_SIZE - 1
        );
    }

    public function testLastValue(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $this->assertSame(
            $collection->last(),
            $generatedObjectList[self::COLLECTION_SIZE - 1]
        );
    }

    public function testHas(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        foreach (array_keys($generatedObjectList) as $key) {
            $this->assertTrue($collection->has($key));
        }
    }

    public function testGetAll(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);
        $collectionEntityList = $collection->getList();

        foreach ($generatedObjectList as $offset => $generatedEntity) {
            $collectionEntity = $collectionEntityList[$offset] ?? null;

            $this->assertNotEmpty($collectionEntity);

            if ($collectionEntity) {
                $this->assertSame(get_class($collectionEntity), get_class($generatedEntity));
            }
        }
    }

    public function testGetAllKeys(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $diff = array_diff(
            array_keys($generatedObjectList),
            $collection->keyList()
        );

        $this->assertCount(0, $diff);
    }

    public function testGetByOffset(): void
    {
        $generatedObjectList = [];
        $generatedObjectListTemp = $this->generateObjectList();
        $generatedObjectListTempKeys = array_keys($generatedObjectListTemp);

        shuffle($generatedObjectListTempKeys);

        foreach ($generatedObjectListTempKeys as $offset) {
            $generatedObjectList[$offset] = clone $generatedObjectListTemp[$offset];

            unset($generatedObjectListTemp[$offset]);
        }

        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $offset = 0;

        foreach ($generatedObjectList as $generatedEntity) {
            $collectionEntity = $collection->getByOffset($offset);

            $this->assertSame($collectionEntity->getId(), $generatedEntity->getId());

            $offset++;
        }
    }

    public function testRemove(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        /** @noinspection PhpAssignmentInConditionInspection */
        while ($generatedEntity = array_shift($generatedObjectList)) {
            $key = $collection->firstKey();

            $collectionEntity = $collection->get($key);

            $collection->remove($key);

            $this->assertSame(count($generatedObjectList), $collection->count());
            $this->assertSame($generatedEntity, $collectionEntity);
        }

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);

        $generatedObjectList = $this->generateObjectList();

        foreach ($generatedObjectList as $generatedEntity) {
            $collection->add($generatedEntity);
        }

        $this->assertSame(count($generatedObjectList), $collection->count());
        $this->assertCount(count($generatedObjectList), $collection->getList());

        /** @noinspection PhpAssignmentInConditionInspection */
        while ($generatedEntity = array_pop($generatedObjectList)) {
            $key = $collection->lastKey();
            $collectionEntity = $collection->get($key);

            $collection->remove($key);

            $this->assertSame(count($generatedObjectList), $collection->count());
            $this->assertSame($generatedEntity, $collectionEntity);
        }

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);
    }

    public function testRemoveAll(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $this->assertCount(count($generatedObjectList), $collection->getList());
        /** @noinspection PhpUnitTestsInspection */
        $this->assertSame($collection->count(), count($generatedObjectList));

        $collection->removeAll();

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);

        foreach ($generatedObjectList as $generatedEntity) {
            $collection->add($generatedEntity);
        }

        $this->assertCount(count($generatedObjectList), $collection->getList());
        /** @noinspection PhpUnitTestsInspection */
        $this->assertSame(count($generatedObjectList), $collection->count());

        $collection->removeAll();

        $this->assertCount(0, $collection->getList());
        $this->assertSame(0, $collection->count());
    }

    public function testMap(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $mapData = $collection->map(
            function (TestEntity $entity) {
                return $entity->getId();
            }
        );

        foreach ($generatedObjectList as $offset => $generatedEntity) {
            $id = $generatedEntity->getId();

            $this->assertArrayHasKey($offset, $mapData);
            $this->assertSame($id, $mapData[$offset]);
        }

        $mapData = $collection->map(
            function (TestEntity $entity) {
                return $entity->getId();
            },
            function (TestEntity $entity) {
                return $entity->getTitle();
            }
        );

        foreach ($generatedObjectList as $generatedEntity) {
            $id = $generatedEntity->getId();
            $title = $generatedEntity->getTitle();

            $this->assertArrayHasKey($id, $mapData);
            $this->assertSame($title, $mapData[$id]);
        }

        $mapData = $collection->map(
            function (TestEntity $entity) {
                return $entity->getId();
            },
            function (TestEntity $entity) {
                return $entity->getTitle();
            },
            function (TestEntity $entity) {
                return $entity->getName();
            }
        );

        foreach ($generatedObjectList as $generatedEntity) {
            $id = $generatedEntity->getId();
            $title = $generatedEntity->getTitle();
            $name = $generatedEntity->getName();

            $this->assertArrayHasKey($name, $mapData);
            $this->assertArrayHasKey($id, $mapData[$name]);
            $this->assertSame($title, $mapData[$name][$id]);
        }
    }

    public function testFilter(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $generatedObjectList = array_values(
            array_filter(
                $generatedObjectList,
                static function (TestEntity $entity) {
                    return $entity->getId() > 2;
                }
            )
        );

        $collection->filter(
            function (TestEntity $entity) {
                return $entity->getId() > 2;
            }
        );

        $this->assertCollection($collection, $generatedObjectList);
    }

    public function testSort(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        usort(
            $generatedObjectList,
            static function (TestEntity $entityA, TestEntity $entityB) {
                return $entityA->getId() < $entityB->getId();
            }
        );

        $collection->sort(
            static function (TestEntity $entityA, TestEntity $entityB) {
                return $entityA->getId() < $entityB->getId();
            }
        );

        $this->assertCollection($collection, $generatedObjectList);
    }

    public function testBatch(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $batchSize = 2;
        $collectionEntityListTotalCount = 0;

        foreach ($collection->batch($batchSize) as $collectionEntityList) {
            $collectionEntityListCount = count($collectionEntityList);

            $this->assertLessThanOrEqual($batchSize, $collectionEntityListCount);

            $collectionEntityListTotalCount += $collectionEntityListCount;
        }

        $this->assertCount($collectionEntityListTotalCount, $generatedObjectList);
    }

    public function testBatchCallback(): void
    {
        $generatedObjectList = $this->generateObjectList();

        $id = self::COLLECTION_SIZE + 1;
        $newEntity = (new TestEntity())
            ->setId($id)
            ->setName('Name ' . $id)
            ->setTitle('Title ' . $id);

        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);
        $collection->setBatchCallback(
            function ($size, callable $defaultCallback, ObjectCollection $that) use ($newEntity) {
                $that->add($newEntity);

                return $defaultCallback($size);
            }
        );

        $generatedObjectList[] = $newEntity;

        $batchSize = 2;
        $collectionEntityListTotalCount = 0;

        foreach ($collection->batch($batchSize) as $collectionEntityList) {
            $collectionEntityListCount = count($collectionEntityList);

            $this->assertLessThanOrEqual($batchSize, $collectionEntityListCount);

            $collectionEntityListTotalCount += $collectionEntityListCount;
        }

        $this->assertCount($collectionEntityListTotalCount, $generatedObjectList);
    }

    public function testBatchCount(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $batchSize = 2;
        $batchCount = (int)ceil(count($generatedObjectList) / $batchSize);

        $collectionBatchCount = $collection->batchCount($batchSize);

        $this->assertSame($collectionBatchCount, $batchCount);
    }

    public function testSlice(): void
    {
        $generatedObjectList = $this->generateObjectList();

        $sliceOffset = 1;
        $sliceLength = count($generatedObjectList) - 2;

        $generatedEntitySliceList = array_slice($generatedObjectList, $sliceOffset, $sliceLength);

        $this->assertLessThanOrEqual($sliceLength, count($generatedEntitySliceList));

        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);
        $collectionEntitySliceList = $collection->slice($sliceOffset, $sliceLength);

        $this->assertLessThanOrEqual($sliceLength, count($collectionEntitySliceList));

        foreach ($generatedEntitySliceList as $i => $generatedEntitySlice) {
            $this->assertNotEmpty($collectionEntitySliceList[$i]);
            $this->assertSame($generatedEntitySlice->getId(), $collectionEntitySliceList[$i]->getId());
        }
    }

    public function testPage(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedObjectList) / $pageLimit);

        $totalCollectionItemsCount = 0;

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $pageItems = $collection->page($pageNumber, $pageLimit);

            $this->assertLessThanOrEqual($pageLimit, count($pageItems));

            $totalCollectionItemsCount += count($pageItems);
        }

        $this->assertCount($totalCollectionItemsCount, $generatedObjectList);
    }

    public function testPageCallback(): void
    {
        $generatedObjectList = $this->generateObjectList();

        $id = self::COLLECTION_SIZE + 1;
        $newEntity = (new TestEntity())
            ->setId($id)
            ->setName('Name ' . $id)
            ->setTitle('Title ' . $id);

        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);
        $collection->setPageCallback(
            function (
                $number,
                $limit,
                $preserveKeys,
                callable $defaultCallback,
                ObjectCollection $that
            ) use (
                $newEntity
            ) {
                $that->add($newEntity);

                return $defaultCallback($number, $limit, $preserveKeys);
            }
        );

        $generatedObjectList[] = $newEntity;

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedObjectList) / $pageLimit);

        $totalCollectionItemsCount = 0;

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $pageItems = $collection->page($pageNumber, $pageLimit);

            $this->assertLessThanOrEqual($pageLimit, count($pageItems));

            $totalCollectionItemsCount += count($pageItems);
        }

        $this->assertCount($totalCollectionItemsCount, $generatedObjectList);
    }

    public function testPageCount(): void
    {
        $generatedObjectList = $this->generateObjectList();
        $collection = new ObjectCollection(TestEntity::class, $generatedObjectList);

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedObjectList) / $pageLimit);

        $collectionPageCount = $collection->pageCount($pageLimit);

        $this->assertSame($collectionPageCount, $pageCount);
    }

    public function testAddOtherEntityException(): void
    {
        $this->expectException(CollectionValidateException::class);

        new ObjectCollection(TestEntity::class, [
            new TestOtherEntity(),
        ]);
    }

    public function testAddInvalidObject(): void
    {
        $this->expectException(CollectionValidateException::class);

        new ObjectCollection(TestEntity::class, [
            new ObjectCollection(TestEntity::class, [
                (object)['test' => 1],
            ]),
        ]);
    }

    public function testSetInvalidItemValue(): void
    {
        $this->expectException(CollectionValidateException::class);

        (new ObjectCollection(TestEntity::class))->set(0, 1);
    }

    protected function assertCollection(
        ObjectCollection $collection,
        array $entityList = null
    ): void {
        $entityList = $entityList ?? $this->generateObjectList();

        $this->assertCount($collection->count(), $entityList);

        foreach ($entityList as $offset => $entity) {
            $collectionEntity = $collection->getByOffset($offset);

            $this->assertNotNull(
                $collectionEntity,
                "\$collection->get({$entity->getId()}) === null"
            );

            if ($collectionEntity !== null) {
                $this->assertSame(get_class($collectionEntity), TestEntity::class);
                $this->assertSame($collectionEntity->getId(), $entity->getId());
                $this->assertSame($collectionEntity->getName(), $entity->getName());
                $this->assertSame($collectionEntity->getTitle(), $entity->getTitle());
            }
        }
    }
}
