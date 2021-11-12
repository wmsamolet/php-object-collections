<?php

namespace Wmsamolet\PhpObjectCollections\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionValidateException;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestEntity;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestIndexedCollection;
use Wmsamolet\PhpObjectCollections\Tests\Fixtures\TestOtherEntity;

final class ExampleIndexedCollectionTest extends TestCase
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
        $collection = new TestIndexedCollection(
            $this->generateEntities()
        );

        $this->assertCollection($collection);
    }

    public function testAdd(): void
    {
        $collection = new TestIndexedCollection();

        foreach ($this->generateEntities() as $entity) {
            $collection->add($entity);
        }

        $this->assertCollection($collection);
    }

    public function testAddList(): void
    {
        $collection = (new TestIndexedCollection())->addList($this->generateEntities());

        $this->assertCollection($collection);
    }

    public function testSet(): void
    {
        $collection = new TestIndexedCollection();

        foreach ($this->generateEntities() as $entity) {
            $collection->set($entity->getId(), $entity);
        }

        $this->assertCollection($collection);
    }

    public function testSetList(): void
    {
        $collection = (new TestIndexedCollection())->setList($this->generateEntities());

        $this->assertCollection($collection);
    }

    public function testSetIterator(): void
    {
        $collection = (new TestIndexedCollection())
            ->setIterator(
                new ArrayIterator($this->generateEntities())
            );

        $this->assertCollection($collection);
    }

    public function testFirstKey(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedCollection($generatedEntityList);

        $this->assertSame(
            $collection->firstKey(),
            $generatedEntityList[0]->getId()
        );
    }

    public function testFirstValue(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedCollection($generatedEntityList);

        $this->assertSame(
            $collection->first(),
            $generatedEntityList[0]
        );
    }

    public function testLastKey(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedCollection($generatedEntityList);

        $this->assertSame(
            $collection->lastKey(),
            $generatedEntityList[self::COLLECTION_SIZE - 1]->getId()
        );
    }

    public function testLastValue(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedCollection($generatedEntityList);

        $this->assertSame(
            $collection->last(),
            $generatedEntityList[self::COLLECTION_SIZE - 1]
        );
    }

    public function testHas(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedCollection($generatedEntityList);

        foreach ($generatedEntityList as $generatedEntity) {
            $this->assertTrue($collection->has($generatedEntity->getId()));
        }
    }

    public function testGetAll(): void
    {
        $generatedEntityList = $this->generateEntities();

        $collection = new TestIndexedCollection($generatedEntityList);

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

        $collection = new TestIndexedCollection($generatedEntityList);

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

        $collection = new TestIndexedCollection($generatedEntityList);

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

        $collection = new TestIndexedCollection($generatedEntityList);

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

        $collection = new TestIndexedCollection($generatedEntityList);

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

        $collection = new TestIndexedCollection($generatedEntityList);

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

        $collection = new TestIndexedCollection($generatedEntityList);

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

        $collection = new TestIndexedCollection($generatedEntityList);

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

        $collection = new TestIndexedCollection($generatedEntityList);

        $batchSize = 2;
        $collectionEntityListTotalCount = 0;

        foreach ($collection->batch($batchSize) as $collectionEntityList) {
            $collectionEntityListCount = count($collectionEntityList);

            $this->assertLessThanOrEqual($batchSize, $collectionEntityListCount);

            $collectionEntityListTotalCount += $collectionEntityListCount;
        }

        $this->assertCount($collectionEntityListTotalCount, $generatedEntityList);
    }

    public function testAddOtherEntityException(): void
    {
        $this->expectException(CollectionValidateException::class);

        new TestIndexedCollection([
            new TestOtherEntity()
        ]);
    }

    public function testAddInvalidObject(): void
    {
        $this->expectException(CollectionValidateException::class);

        new TestIndexedCollection([
            (object)['test' => 1]
        ]);
    }

    public function testSetInvalidItemValue(): void
    {
        $this->expectException(CollectionValidateException::class);

        (new TestIndexedCollection())->set(0, 1);
    }

    protected function assertCollection(
        TestIndexedCollection $collection,
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
