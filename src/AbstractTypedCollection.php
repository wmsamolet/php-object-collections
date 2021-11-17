<?php

namespace Wmsamolet\PhpObjectCollections;

use Wmsamolet\PhpObjectCollections\Exceptions\CollectionException;

abstract class AbstractTypedCollection extends AbstractCollection
{
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_STRING = 'string';
    public const TYPE_ARRAY = 'array';
    public const TYPE_OBJECT = 'object';
    public const TYPE_RESOURCE = 'resource';
    public const TYPE_RESOURCE_CLOSED = 'resource (closed)';
    public const TYPE_NULL = 'NULL';

    abstract public function collectionValueType(): string;

    public static function typeList(): array
    {
        $typeList = [
            static::TYPE_BOOLEAN,
            static::TYPE_INTEGER,
            static::TYPE_DOUBLE,
            static::TYPE_STRING,
            static::TYPE_ARRAY,
            static::TYPE_OBJECT,
            static::TYPE_RESOURCE,
            static::TYPE_RESOURCE_CLOSED,
            static::TYPE_NULL,
        ];

        return array_combine($typeList, $typeList);
    }

    public function collectionKeyType(): string
    {
        return static::TYPE_INTEGER;
    }

    public function validate($value, $key = null, bool $throwException = false): bool
    {
        $valueType = gettype($value);
        $collectionValueType = $this->collectionValueType();
        $isValid = $valueType === $collectionValueType;

        if (!$isValid && $throwException) {
            throw new CollectionException(
                "Collection item value type must be \"$collectionValueType\" but \"$valueType\" given"
            );
        }

        if ($isValid) {
            $keyType = gettype($key);
            $collectionKeyType = $this->collectionKeyType();
            $isValid = $keyType === $collectionKeyType;

            if (!$isValid && $throwException) {
                throw new CollectionException(
                    "Collection item key type must be \"$collectionKeyType\" but \"$valueType\" given"
                );
            }
        }

        return $isValid;
    }
}
