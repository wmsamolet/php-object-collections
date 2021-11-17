<?php

namespace Wmsamolet\PhpObjectCollections;

/**
 * @method object[] getList()
 * @method null|object get(int $key)
 * @method null|object getByOffset(int $offset)
 */
class ObjectCollection extends AbstractObjectCollection
{
    /**
     * @var string
     */
    protected $objectClassName;
    /**
     * @var callable|null
     */
    protected $convertKeyCallback;
    /**
     * @var callable|null
     */
    protected $convertValueCallback;

    public function collectionObjectClassName(): string
    {
        return $this->objectClassName;
    }

    public function __construct(
        string $objectClassName,
        array $items = [],
        callable $convertKeyCallback = null,
        callable $convertValueCallback = null
    ) {
        $this->objectClassName = $objectClassName;
        $this->convertKeyCallback = $convertKeyCallback;
        $this->convertValueCallback = $convertValueCallback;

        parent::__construct($items);
    }

    protected function convertKey($key, $formattedValue)
    {
        return $this->convertKeyCallback !== null
            ? call_user_func($this->convertKeyCallback, $key, $formattedValue)
            : parent::convertKey($key, $formattedValue);
    }

    protected function convertValue($value)
    {
        return $this->convertValueCallback !== null
            ? call_user_func($this->convertValueCallback, $value)
            : parent::convertValue($value);
    }
}
