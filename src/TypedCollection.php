<?php

namespace Wmsamolet\PhpObjectCollections;

use Wmsamolet\PhpObjectCollections\Exceptions\CollectionException;

class TypedCollection extends AbstractTypedCollection
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var callable|null
     */
    protected $convertKeyCallback;

    public function collectionValueType(): string
    {
        return $this->type;
    }

    public function __construct(string $type, array $items = [], callable $convertKeyCallback = null)
    {
        if (!in_array($type, static::typeList(), true)) {
            throw new CollectionException("Invalid collection items type \"$type\"");
        }

        $this->type = $type;
        $this->convertKeyCallback = $convertKeyCallback;

        parent::__construct($items);
    }

    protected function convertKey($key, $formattedValue)
    {
        return $this->convertKeyCallback !== null
            ? call_user_func($this->convertKeyCallback, $key, $formattedValue)
            : parent::convertKey($key, $formattedValue);
    }
}
