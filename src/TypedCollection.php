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
    /**
     * @var callable|null
     */
    protected $convertValueCallback;

    public function collectionValueType(): string
    {
        return $this->type;
    }

    public function __construct(
        string $type,
        array $items = [],
        callable $convertKeyCallback = null,
        callable $convertValueCallback = null
    ) {
        if (!in_array($type, static::typeList(), true)) {
            throw new CollectionException("Invalid collection items type \"$type\"");
        }

        $this->type = $type;
        $this->convertKeyCallback = $convertKeyCallback;
        $this->convertValueCallback = $convertValueCallback;

        parent::__construct($items);
    }

    /**
     * @return callable|null
     */
    public function getConvertKeyCallback(): ?callable
    {
        return $this->convertKeyCallback;
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function setConvertKeyCallback(?callable $convertKeyCallback)
    {
        $this->convertKeyCallback = $convertKeyCallback;

        return $this;
    }

    /**
     * @return callable|null
     */
    public function getConvertValueCallback(): ?callable
    {
        return $this->convertValueCallback;
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function setConvertValueCallback(?callable $convertValueCallback)
    {
        $this->convertValueCallback = $convertValueCallback;

        return $this;
    }

    protected function convertKey($key, $convertedValue)
    {
        return $this->convertKeyCallback !== null
            ? call_user_func($this->convertKeyCallback, $key, $convertedValue)
            : parent::convertKey($key, $convertedValue);
    }

    protected function convertValue($value)
    {
        return $this->convertValueCallback !== null
            ? call_user_func($this->convertValueCallback, $value)
            : parent::convertValue($value);
    }
}
