<?php

namespace Wmsamolet\PhpObjectCollections\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionValidateException;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestEntity;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestEntityCollection;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestOtherEntity;

final class TestEntityCollectionTest extends TestCase
{
    private const COLLECTION_SIZE = 5;

    /**
     * @return TestEntity[]
     */
    public function generateEntityList(): array
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
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $this->assertCollection($collection, $generatedEntityList);
    }

    public function testAdd(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection();

        foreach ($generatedEntityList as $entity) {
            $collection->add($entity);
        }

        $this->assertCollection($collection, $generatedEntityList);
    }

    public function testAddList(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = (new TestEntityCollection())->addList($generatedEntityList);

        $this->assertCollection($collection, $generatedEntityList);
    }

    public function testSet(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection();

        foreach ($generatedEntityList as $entity) {
            $collection->set($entity->getId(), $entity);
        }

        $this->assertCollection($collection, $generatedEntityList);
    }

    public function testSetList(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = (new TestEntityCollection())->setList($generatedEntityList);

        $this->assertCollection($collection, $generatedEntityList);
    }

    public function testSetIterator(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = (new TestEntityCollection())
            ->setIterator(
                new ArrayIterator($generatedEntityList)
            );

        $this->assertCollection($collection, $generatedEntityList);
    }

    public function testFirstKey(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $this->assertSame($collection->firstKey(), 0);
    }

    public function testFirstValue(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $this->assertSame(
            $collection->first(),
            $generatedEntityList[0]
        );
    }

    public function testLastKey(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $this->assertSame(
            $collection->lastKey(),
            self::COLLECTION_SIZE - 1
        );
    }

    public function testLastValue(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $this->assertSame(
            $collection->last(),
            $generatedEntityList[self::COLLECTION_SIZE - 1]
        );
    }

    public function testHas(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        foreach (array_keys($generatedEntityList) as $key) {
            $this->assertTrue($collection->has($key));
        }
    }

    public function testGetAll(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);
        $collectionEntityList = $collection->getList();

        foreach ($generatedEntityList as $offset => $generatedEntity) {
            $collectionEntity = $collectionEntityList[$offset] ?? null;

            $this->assertNotEmpty($collectionEntity);

            if ($collectionEntity) {
                $this->assertSame(get_class($collectionEntity), get_class($generatedEntity));
            }
        }
    }

    public function testGetAllKeys(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $diff = array_diff(
            array_keys($generatedEntityList),
            $collection->keyList()
        );

        $this->assertCount(0, $diff);
    }

    public function testGetByOffset(): void
    {
        $generatedEntityList = [];
        $generatedEntityListTemp = $this->generateEntityList();
        $generatedEntityListTempKeys = array_keys($generatedEntityListTemp);

        shuffle($generatedEntityListTempKeys);

        foreach ($generatedEntityListTempKeys as $offset) {
            $generatedEntityList[$offset] = clone $generatedEntityListTemp[$offset];

            unset($generatedEntityListTemp[$offset]);
        }

        $collection = new TestEntityCollection($generatedEntityList);

        $offset = 0;

        foreach ($generatedEntityList as $generatedEntity) {
            $collectionEntity = $collection->getByOffset($offset);

            $this->assertSame($collectionEntity->getId(), $generatedEntity->getId());

            $offset++;
        }
    }

    public function testRemove(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        /** @noinspection PhpAssignmentInConditionInspection */
        while ($generatedEntity = array_shift($generatedEntityList)) {
            $key = $collection->firstKey();

            $collectionEntity = $collection->get($key);

            $collection->remove($key);

            $this->assertSame(count($generatedEntityList), $collection->count());
            $this->assertSame($generatedEntity, $collectionEntity);
        }

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);

        $generatedEntityList = $this->generateEntityList();

        foreach ($generatedEntityList as $generatedEntity) {
            $collection->add($generatedEntity);
        }

        $this->assertSame(count($generatedEntityList), $collection->count());
        $this->assertCount(count($generatedEntityList), $collection->getList());

        /** @noinspection PhpAssignmentInConditionInspection */
        while ($generatedEntity = array_pop($generatedEntityList)) {
            $key = $collection->lastKey();
            $collectionEntity = $collection->get($key);

            $collection->remove($key);

            $this->assertSame(count($generatedEntityList), $collection->count());
            $this->assertSame($generatedEntity, $collectionEntity);
        }

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);
    }

    public function testRemoveAll(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $this->assertCount(count($generatedEntityList), $collection->getList());
        /** @noinspection PhpUnitTestsInspection */
        $this->assertSame($collection->count(), count($generatedEntityList));

        $collection->removeAll();

        $this->assertCount(0, $collection->getList());
        $this->assertSame($collection->count(), 0);

        foreach ($generatedEntityList as $generatedEntity) {
            $collection->add($generatedEntity);
        }

        $this->assertCount(count($generatedEntityList), $collection->getList());
        /** @noinspection PhpUnitTestsInspection */
        $this->assertSame(count($generatedEntityList), $collection->count());

        $collection->removeAll();

        $this->assertCount(0, $collection->getList());
        $this->assertSame(0, $collection->count());
    }

    public function testMap(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $mapData = $collection->map(
            function (TestEntity $entity) {
                return $entity->getId();
            }
        );

        foreach ($generatedEntityList as $offset => $generatedEntity) {
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

        foreach ($generatedEntityList as $generatedEntity) {
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

        foreach ($generatedEntityList as $generatedEntity) {
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
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $generatedEntityList = array_values(
            array_filter(
                $generatedEntityList,
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

        $this->assertCollection($collection, $generatedEntityList);
    }

    public function testSort(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        usort(
            $generatedEntityList,
            static function (TestEntity $entityA, TestEntity $entityB) {
                return $entityA->getId() < $entityB->getId();
            }
        );

        $collection->sort(
            static function (TestEntity $entityA, TestEntity $entityB) {
                return $entityA->getId() < $entityB->getId();
            }
        );

        $this->assertCollection($collection, $generatedEntityList);
    }

    public function testBatch(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $batchSize = 2;
        $collectionEntityListTotalCount = 0;

        foreach ($collection->batch($batchSize) as $collectionEntityList) {
            $collectionEntityListCount = count($collectionEntityList);

            $this->assertLessThanOrEqual($batchSize, $collectionEntityListCount);

            $collectionEntityListTotalCount += $collectionEntityListCount;
        }

        $this->assertCount($collectionEntityListTotalCount, $generatedEntityList);
    }

    public function testBatchCallback(): void
    {
        $generatedEntityList = $this->generateEntityList();

        $id = self::COLLECTION_SIZE + 1;
        $newEntity = (new TestEntity())
            ->setId($id)
            ->setName('Name ' . $id)
            ->setTitle('Title ' . $id);

        $collection = new TestEntityCollection($generatedEntityList);
        $collection->setBatchCallback(
            function ($size, callable $defaultCallback, TestEntityCollection $that) use ($newEntity) {
                $that->add($newEntity);

                return $defaultCallback($size);
            }
        );

        $generatedEntityList[] = $newEntity;

        $batchSize = 2;
        $collectionEntityListTotalCount = 0;

        foreach ($collection->batch($batchSize) as $collectionEntityList) {
            $collectionEntityListCount = count($collectionEntityList);

            $this->assertLessThanOrEqual($batchSize, $collectionEntityListCount);

            $collectionEntityListTotalCount += $collectionEntityListCount;
        }

        $this->assertCount($collectionEntityListTotalCount, $generatedEntityList);
    }

    public function testBatchCount(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $batchSize = 2;
        $batchCount = (int)ceil(count($generatedEntityList) / $batchSize);

        $collectionBatchCount = $collection->batchCount($batchSize);

        $this->assertSame($collectionBatchCount, $batchCount);
    }

    public function testSlice(): void
    {
        $generatedEntityList = $this->generateEntityList();

        $sliceOffset = 1;
        $sliceLength = count($generatedEntityList) - 2;

        $generatedEntitySliceList = array_slice($generatedEntityList, $sliceOffset, $sliceLength);

        $this->assertLessThanOrEqual($sliceLength, count($generatedEntitySliceList));

        $collection = new TestEntityCollection($generatedEntityList);
        $collectionEntitySliceList = $collection->slice($sliceOffset, $sliceLength);

        $this->assertLessThanOrEqual($sliceLength, count($collectionEntitySliceList));

        foreach ($generatedEntitySliceList as $i => $generatedEntitySlice) {
            $this->assertNotEmpty($collectionEntitySliceList[$i]);
            $this->assertSame($generatedEntitySlice->getId(), $collectionEntitySliceList[$i]->getId());
        }
    }

    public function testPage(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedEntityList) / $pageLimit);

        $totalCollectionItemsCount = 0;

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $pageItems = $collection->page($pageNumber, $pageLimit);

            $this->assertLessThanOrEqual($pageLimit, count($pageItems));

            $totalCollectionItemsCount += count($pageItems);
        }

        $this->assertCount($totalCollectionItemsCount, $generatedEntityList);
    }

    public function testPageCallback(): void
    {
        $generatedEntityList = $this->generateEntityList();

        $id = self::COLLECTION_SIZE + 1;
        $newEntity = (new TestEntity())
            ->setId($id)
            ->setName('Name ' . $id)
            ->setTitle('Title ' . $id);

        $collection = new TestEntityCollection($generatedEntityList);
        $collection->setPageCallback(
            function (
                $number,
                $limit,
                $preserveKeys,
                callable $defaultCallback,
                TestEntityCollection $that
            ) use (
                $newEntity
            ) {
                $that->add($newEntity);

                return $defaultCallback($number, $limit, $preserveKeys);
            }
        );

        $generatedEntityList[] = $newEntity;

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedEntityList) / $pageLimit);

        $totalCollectionItemsCount = 0;

        for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
            $pageItems = $collection->page($pageNumber, $pageLimit);

            $this->assertLessThanOrEqual($pageLimit, count($pageItems));

            $totalCollectionItemsCount += count($pageItems);
        }

        $this->assertCount($totalCollectionItemsCount, $generatedEntityList);
    }

    public function testPageCount(): void
    {
        $generatedEntityList = $this->generateEntityList();
        $collection = new TestEntityCollection($generatedEntityList);

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedEntityList) / $pageLimit);

        $collectionPageCount = $collection->pageCount($pageLimit);

        $this->assertSame($collectionPageCount, $pageCount);
    }

    public function testAddOtherEntityException(): void
    {
        $this->expectException(CollectionValidateException::class);

        new TestEntityCollection([
            new TestOtherEntity(),
        ]);
    }

    public function testAddInvalidObject(): void
    {
        $this->expectException(CollectionValidateException::class);

        new TestEntityCollection([
            (object)['test' => 1],
        ]);
    }

    public function testSetInvalidItemValue(): void
    {
        $this->expectException(CollectionValidateException::class);

        (new TestEntityCollection())->set(0, 1);
    }

    protected function assertCollection(
        TestEntityCollection $collection,
        array $entityList = null
    ): void {
        $entityList = $entityList ?? $this->generateEntityList();

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
