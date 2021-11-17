<?php

namespace Wmsamolet\PhpObjectCollections\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionValidateException;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestEntity;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestIndexedEntityCollection;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestOtherEntity;

final class TestIndexedEntityCollectionTest extends TestCase
{
    private const COLLECTION_SIZE = 5;

    /**
     * @return TestEntity[]
     */
    public function generateEntities(): array
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
        $collection = new TestIndexedEntityCollection(
            $this->generateEntities()
        );

        $this->assertCollection($collection);
    }

    public function testAdd(): void
    {
        $collection = new TestIndexedEntityCollection();

        foreach ($this->generateEntities() as $entity) {
            $collection->add($entity);
        }

        $this->assertCollection($collection);
    }

    public function testAddList(): void
    {
        $collection = (new TestIndexedEntityCollection())->addList($this->generateEntities());

        $this->assertCollection($collection);
    }

    public function testSet(): void
    {
        $collection = new TestIndexedEntityCollection();

        foreach ($this->generateEntities() as $entity) {
            $collection->set($entity->getId(), $entity);
        }

        $this->assertCollection($collection);
    }

    public function testSetList(): void
    {
        $collection = (new TestIndexedEntityCollection())->setList($this->generateEntities());

        $this->assertCollection($collection);
    }

    public function testSetIterator(): void
    {
        $collection = (new TestIndexedEntityCollection())
            ->setIterator(
                new ArrayIterator($this->generateEntities())
            );

        $this->assertCollection($collection);
    }

    public function testFirstKey(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $this->assertSame(
            $collection->firstKey(),
            $generatedEntityList[0]->getId()
        );
    }

    public function testFirstValue(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $this->assertSame(
            $collection->first(),
            $generatedEntityList[0]
        );
    }

    public function testLastKey(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $this->assertSame(
            $collection->lastKey(),
            $generatedEntityList[self::COLLECTION_SIZE - 1]->getId()
        );
    }

    public function testLastValue(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $this->assertSame(
            $collection->last(),
            $generatedEntityList[self::COLLECTION_SIZE - 1]
        );
    }

    public function testHas(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        foreach ($generatedEntityList as $generatedEntity) {
            $this->assertTrue($collection->has($generatedEntity->getId()));
        }
    }

    public function testGetAll(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $collectionEntityList = $collection->getList();

        foreach ($generatedEntityList as $generatedEntity) {
            $collectionEntity = $collectionEntityList[$generatedEntity->getId()] ?? null;

            $this->assertNotEmpty($collectionEntity);

            if ($collectionEntity) {
                $this->assertSame(get_class($collectionEntity), get_class($generatedEntity));
            }
        }
    }

    public function testGetAllKeys(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $diff = array_diff(
            array_map(
                static function (TestEntity $entity) {
                    return $entity->getId();
                },
                $generatedEntityList
            ),
            $collection->keyList()
        );

        $this->assertCount(0, $diff);
    }

    public function testGetByOffset(): void
    {
        $generatedEntityList = [];
        $generatedEntityListTemp = $this->generateEntities();
        $generatedEntityListTempKeys = array_keys($generatedEntityListTemp);

        shuffle($generatedEntityListTempKeys);

        foreach ($generatedEntityListTempKeys as $offset) {
            $generatedEntityList[$offset] = clone $generatedEntityListTemp[$offset];

            unset($generatedEntityListTemp[$offset]);
        }

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $offset = 0;

        foreach ($generatedEntityList as $generatedEntity) {
            $collectionEntity = $collection->getByOffset($offset);

            $this->assertSame($collectionEntity->getId(), $generatedEntity->getId());

            $offset++;
        }
    }

    public function testRemove(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

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

        $generatedEntityList = $this->generateEntities();

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
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

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
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

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
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

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
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

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
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

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
        $generatedEntityList = $this->generateEntities();

        $id = self::COLLECTION_SIZE + 1;
        $newEntity = (new TestEntity())
            ->setId($id)
            ->setName('Name ' . $id)
            ->setTitle('Title ' . $id);

        $collection = new TestIndexedEntityCollection($generatedEntityList);
        $collection->setBatchCallback(
            function ($size, callable $defaultCallback, TestIndexedEntityCollection $that) use ($newEntity) {
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
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $batchSize = 2;
        $batchCount = (int)ceil(count($generatedEntityList) / $batchSize);

        $collectionBatchCount = $collection->batchCount($batchSize);

        $this->assertSame($collectionBatchCount, $batchCount);
    }

    public function testSlice(): void
    {
        $generatedEntityList = $this->generateEntities();

        $sliceOffset = 1;
        $sliceLength = count($generatedEntityList) - 2;

        $generatedEntitySliceList = array_slice($generatedEntityList, $sliceOffset, $sliceLength);

        $this->assertLessThanOrEqual($sliceLength, count($generatedEntitySliceList));

        $collection = new TestIndexedEntityCollection($generatedEntityList);
        $collectionEntitySliceList = $collection->slice($sliceOffset, $sliceLength);

        $this->assertLessThanOrEqual($sliceLength, count($collectionEntitySliceList));

        foreach ($generatedEntitySliceList as $i => $generatedEntitySlice) {
            $this->assertNotEmpty($collectionEntitySliceList[$i]);
            $this->assertSame($generatedEntitySlice->getId(), $collectionEntitySliceList[$i]->getId());
        }
    }

    public function testPage(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

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
        $generatedEntityList = $this->generateEntities();

        $id = self::COLLECTION_SIZE + 1;
        $newEntity = (new TestEntity())
            ->setId($id)
            ->setName('Name ' . $id)
            ->setTitle('Title ' . $id);

        $collection = new TestIndexedEntityCollection($generatedEntityList);
        $collection->setPageCallback(
            function (
                $number,
                $limit,
                $preserveKeys,
                callable $defaultCallback,
                TestIndexedEntityCollection $that
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
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedEntityCollection($generatedEntityList);

        $pageLimit = 2;
        $pageCount = (int)ceil(count($generatedEntityList) / $pageLimit);

        $collectionPageCount = $collection->pageCount($pageLimit);

        $this->assertSame($collectionPageCount, $pageCount);
    }

    public function testAddOtherEntityException(): void
    {
        $this->expectException(CollectionValidateException::class);

        new TestIndexedEntityCollection([
            new TestOtherEntity()
        ]);
    }

    public function testAddInvalidObject(): void
    {
        $this->expectException(CollectionValidateException::class);

        new TestIndexedEntityCollection([
            (object)['test' => 1]
        ]);
    }

    public function testSetInvalidItemValue(): void
    {
        $this->expectException(CollectionValidateException::class);

        (new TestIndexedEntityCollection())->set(0, 1);
    }

    protected function assertCollection(
        TestIndexedEntityCollection $collection,
        array $entityList = null
    ): void {
        $entityList = $entityList ?? $this->generateEntities();

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
