<?php

namespace Wmsamolet\PhpObjectCollections\Tests\Fixtures;

use Wmsamolet\PhpObjectCollections\AbstractObjectCollection;

/**
 * @method TestEntity[] getList()
 * @method null|TestEntity get(int $key)
 * @method null|TestEntity getByOffset(int $offset)
 */
class TestIndexedCollection extends AbstractObjectCollection
{
    protected function collectionObjectClassName(): string
    {
        return TestEntity::class;
    }

    /**
     * @param int|string|true $key
     * @param TestEntity $formattedValue
     */
    protected function convertKey($key, $formattedValue): int
    {
        return $formattedValue->getId();
    }
}
