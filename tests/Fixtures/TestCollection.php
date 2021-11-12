<?php

namespace Wmsamolet\PhpObjectCollections\Tests\Fixtures;

use Wmsamolet\PhpObjectCollections\AbstractObjectCollection;

/**
 * @method TestEntity[] getList()
 * @method null|TestEntity get(int $key)
 * @method null|TestEntity getByOffset(int $offset)
 */
class TestCollection extends AbstractObjectCollection
{
    protected function collectionObjectClassName(): string
    {
        return TestEntity::class;
    }
}
