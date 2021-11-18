<?php

/** @noinspection PhpUnusedParameterInspection */

namespace Wmsamolet\PhpObjectCollections;

use ArrayIterator;
use Throwable;
use Traversable;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionConvertException;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionException;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionValidateException;

abstract class AbstractCollection implements CollectionInterface
{
    /**
     * @var ArrayIteratorIterator
     */
    private $iterator;
    /**
     * @var null|callable
     */
    private $batchCallback;
    /**
     * @var null|callable
     */
    private $pageCallback;
    /**
     * @var null|callable
     */
    private $countCallback;

    public function __construct(array $items = [])
    {
        $this->setIterator(new ArrayIterator());

        if (count($items)) {
            $this->setList($items);
        }
    }

    public function __clone()
    {
        $this->iterator = (new ArrayIteratorIterator(clone $this->iterator->getInnerIterator()))
            ->setDataArrayIterator(
                new ArrayIterator(
                    $this->iterator->getDataArrayIterator()->getArrayCopy()
                )
            );
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public static function fromIterator(Traversable $iterator)
    {
        return (new static())->setIterator($iterator);
    }

    /**
     * @inheritdoc
     */
    public function setIterator(Traversable $iterator)
    {
        if (!is_subclass_of($this->iterator, ArrayIteratorIterator::class)) {
            $this->iterator = new ArrayIteratorIterator($iterator);
        }

        $this->iterator->setKeyGenerator(
            function ($key, $value) {
                return $this->convertKey(
                    $key,
                    $this->convertValue($value)
                );
            }
        );

        return $this;
    }

    /**
     * Gets an iterator of data collection
     */
    public function getIterator(): ArrayIteratorIterator
    {
        return $this->iterator;
    }

    /**
     * @inheritdoc
     */
    public function setCountCallback(callable $callback)
    {
        $this->countCallback = $callback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCountCallback(): ?callable
    {
        return $this->countCallback;
    }

    /**
     * @inheritdoc
     */
    public function setBatchCallback(?callable $batchCallback)
    {
        $this->batchCallback = $batchCallback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBatchCallback(): ?callable
    {
        return $this->batchCallback;
    }

    /**
     * @inheritdoc
     */
    public function setPageCallback(?callable $callback)
    {
        $this->pageCallback = $callback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPageCallback(): ?callable
    {
        return $this->pageCallback;
    }

    /**
     * @inheritdoc
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * @inheritdoc
     */
    public function offset(): int
    {
        return $this->iterator->offset();
    }

    /**
     * @inheritdoc
     */
    public function offsetKey(int $offset): ?int
    {
        return $this->iterator->offsetKey($offset);
    }

    /**
     * @inheritdoc
     */
    public function add($value)
    {
        $this->set(true, $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addList(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        if ($key === null) {
            throw new CollectionException('Invalid key is NULL');
        }

        try {
            $formattedValue = $this->convertValue($value);
            $formattedKey = $this->convertKey($key, $formattedValue);
        } catch (Throwable $convertException) {
            $convertException = new CollectionConvertException(
                $convertException->getMessage(),
                $convertException->getCode(),
                $convertException
            );

            $formattedValue = $value;
            $formattedKey = $key;
        }

        try {
            if (!$this->validate($formattedValue, $formattedKey, true)) {
                throw new CollectionValidateException(
                    'Invalid set collection "' . static::class . "\" value with key \"$formattedKey\"",
                    500,
                    $convertException ?? null
                );
            }
        } catch (Throwable $validateException) {
            throw new CollectionValidateException(
                $validateException->getMessage(),
                $validateException->getCode(),
                $validateException
            );
        }

        if (isset($convertException)) {
            throw $convertException;
        }

        $this->iterator->offsetSet($formattedKey, $formattedValue);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setList(array $items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function has($key): bool
    {
        return $this->iterator->offsetExists($key);
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->convertValue(
            $this->iterator->offsetGet($key)
        );
    }

    public function getByOffset(int $offset)
    {
        $offsetKey = $this->offsetKey($offset);

        if ($offsetKey === null) {
            throw new CollectionException(
                'Collection item not defined at offset: ' . $offset
            );
        }

        return $this->get($offsetKey);
    }

    /**
     * @inheritdoc
     */
    public function getList(): array
    {
        $result = [];

        $this->rewind();

        /** @var int|string $key */
        foreach ($this as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function keyList(): array
    {
        return $this->iterator->keyList();
    }

    /**
     * @inheritdoc
     */
    public function firstKey()
    {
        return $this->iterator->firstKey();
    }

    /**
     * @inheritdoc
     */
    public function first()
    {
        return $this->get($this->firstKey());
    }

    /**
     * @inheritdoc
     */
    public function lastKey()
    {
        return $this->iterator->lastKey();
    }

    /**
     * @inheritdoc
     */
    public function last()
    {
        return $this->get($this->lastKey());
    }

    /**
     * @inheritdoc
     */
    public function remove($key)
    {
        $this->iterator->offsetUnset($key);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeAll()
    {
        $this->setIterator(new ArrayIterator());

        return $this;
    }

    /**
     * Converts the collection items data to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        /** @var int|string $key */
        foreach ($this->getList() as $key => $value) {
            if (
                is_subclass_of($value, ArrayableInterface::class)
                ||
                is_subclass_of($value, CollectionInterface::class)
            ) {
                $result[$key] = $value->toArray();
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function validate($value, $key = null, bool $throwException = false): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function batch(int $size): iterable
    {
        $defaultCallback = function ($size) {
            $i = 0;
            $batch = [];

            $this->iterator->rewind();

            /** @var int|string $key */
            foreach ($this as $key => $value) {
                $batch[$key] = $value;

                if (++$i % $size === 0) {
                    yield $batch;

                    $batch = [];
                }
            }

            yield $batch;
        };

        if ($this->batchCallback) {
            $callback = $this->batchCallback;
        } else {
            $callback = $defaultCallback;
            $defaultCallback = null;
        }

        return $callback($size, $defaultCallback, $this);
    }

    /**
     * @inheritdoc
     */
    public function batchCount(int $size): int
    {
        return (int)ceil($this->count() / $size);
    }

    /**
     * @inheritdoc
     */
    public function slice(int $offset, int $length, bool $preserveKeys = false): ?array
    {
        $resultData = [];

        $offsetFrom = $offset;
        $offsetTo = $offsetFrom + ($length - 1);

        for ($o = $offsetFrom; $o <= $offsetTo; $o++) {
            $offsetKey = $this->offsetKey($o);

            if ($offsetKey === null) {
                break;
            }

            if ($preserveKeys) {
                $resultData[$offsetKey] = $this->get($offsetKey);
            } else {
                $resultData[] = $this->get($offsetKey);
            }
        }

        return count($resultData) ? $resultData : null;
    }

    /**
     * @inheritdoc
     */
    public function page(int $number, int $limit, bool $preserveKeys = false): ?array
    {
        $defaultCallback = function ($number, $limit, $preserveKeys) {
            $number = $number > 0 ? $number : 1;

            $offset = ($number - 1) * $limit;

            return $this->slice($offset, $limit, $preserveKeys);
        };

        if ($this->pageCallback) {
            $callback = $this->pageCallback;
        } else {
            $callback = $defaultCallback;
            $defaultCallback = null;
        }

        return $callback($number, $limit, $preserveKeys, $defaultCallback, $this);
    }

    /**
     * @inheritdoc
     */
    public function pageCount(int $limit): int
    {
        return $this->batchCount($limit);
    }

    /**
     * @inheritdoc
     */
    public function map($keyOrValue, $value = null, $group = null): array
    {
        $result = [];

        if ($value === null) {
            $value = $keyOrValue;

            $i = 0;
            $keyOrValue = static function ($item, $context) use (&$i) {
                return $i++;
            };
        }

        $this->rewind();

        foreach ($this as $item) {
            /** @var int|string $keyData */
            $keyData = $keyOrValue($item, $this);
            $valueData = $value($item, $this);

            if ($group !== null) {
                $groupData = $group($item, $this);

                if (!isset($result[$groupData])) {
                    $result[$groupData] = [];
                }

                $result[$groupData][$keyData] = $valueData;
            } else {
                $result[$keyData] = $valueData;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $filterCallback)
    {
        $removeKeyList = [];

        $this->rewind();

        foreach ($this as $key => $value) {
            $isValid = (bool)$filterCallback($value, $key);

            if (!$isValid) {
                $removeKeyList[] = $key;
            }
        }

        foreach ($removeKeyList as $removeKey) {
            $this->remove($removeKey);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function sort(callable $callback, bool $byKey = false)
    {
        $this->iterator->uksort(
            function ($a, $b) use ($callback, $byKey) {
                $a = $this->convertKey($a, $this->iterator->offsetGet($a));
                $b = $this->convertKey($b, $this->iterator->offsetGet($b));

                if (!$byKey) {
                    $a = $this->get($a);
                    $b = $this->get($b);
                }

                return $callback($a, $b);
            }
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->convertKey(
            $this->iterator->key(),
            $this->current()
        );
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->convertValue(
            $this->iterator->current()
        );
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        $this->iterator->next();
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * Gets the number of objects in the collection
     *
     * @param bool $useCountCallback
     *
     * @return int
     */
    public function count(bool $useCountCallback = true): int
    {
        return $useCountCallback && is_callable($this->countCallback)
            ? call_user_func($this->countCallback, $this)
            : $this->iterator->count();
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Convert each collection item key on:
     * ->get($key)
     * ->getAll()
     * ->add($value)
     * ->set($key, $value)
     * ->setAll($values)
     *
     * @param int|string $key
     *
     * @return int|string
     */
    protected function convertKey($key, $formattedValue)
    {
        return $key === true ? $this->count() : $key;
    }

    /**
     * Converts item value when setting and getting data from the collection
     */
    protected function convertValue($value)
    {
        return $value;
    }
}
