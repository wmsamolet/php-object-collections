<?php

namespace Wmsamolet\PhpObjectCollections\Tests\Fixtures;

use Wmsamolet\PhpObjectCollections\AbstractTypedCollection;

/**
 * @method int[] getList()
 * @method null|int get(int $key)
 * @method null|int getByOffset(int $offset)
 */
class TestIntTypedCollection extends AbstractTypedCollection
{
    public function collectionValueType(): string
    {
        return static::TYPE_INTEGER;
    }
}
