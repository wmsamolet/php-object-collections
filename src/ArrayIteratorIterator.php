<?php

/** @noinspection PhpUnused, UnknownInspectionInspection */

namespace Wmsamolet\PhpObjectCollections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorIterator;
use Wmsamolet\PhpObjectCollections\Exceptions\CollectionException;

/**
 * This iterator wrapper allows the conversion of anything that is
 * Traversable into an ArrayIterator {@see ArrayIterator}.
 *
 * @link https://www.php.net/manual/en/class.iteratoriterator.php
 *
 * @mixin ArrayIterator
 */
class ArrayIteratorIterator extends IteratorIterator implements ArrayAccess, Countable
{
    /** @var int[]|string[] */
    private $keyList = [];

    /** @var null|callable */
    private $keyGenerator;

    /** @var \ArrayIterator */
    protected $dataArrayIterator;

    public function getDataArrayIterator(): ArrayIterator
    {
        $this->loadData();

        return $this->dataArrayIterator;
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function setDataArrayIterator(ArrayIterator $arrayIterator)
    {
        $this->dataArrayIterator = $arrayIterator;

        $arrayIteratorKeyList = array_keys($arrayIterator->getArrayCopy());

        $this->keyList = array_combine($arrayIteratorKeyList, $arrayIteratorKeyList);

        return $this;
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function setKeyGenerator(?callable $keyGenerator)
    {
        $this->keyGenerator = $keyGenerator;

        return $this;
    }

    public function getKeyGenerator(): ?callable
    {
        return $this->keyGenerator;
    }

    public function offset($key = null): ?int
    {
        $offset = array_search(
            $key ?? $this->key(),
            array_values($this->keyList),
            true
        );

        return $offset !== false ? $offset : null;
    }

    public function offsetKey(int $offset): ?int
    {
        return array_values($this->keyList)[$offset] ?? null;
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::offsetExists()
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function offsetExists($key): bool
    {
        $this->loadData();

        return $this->dataArrayIterator->offsetExists($key);
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::offsetGet()
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function offsetGet($key)
    {
        $this->loadData();

        return $this->dataArrayIterator->offsetGet($key);
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::offsetSet()
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function offsetSet($key, $value): void
    {
        $this->loadData();

        $this->dataArrayIterator->offsetSet($key, $value);
        $this->keyList[$key] = $key;
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::offsetUnset()
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function offsetUnset($key): void
    {
        $this->loadData();

        $this->dataArrayIterator->offsetUnset($key);

        unset($this->keyList[$key]);
    }

    /**
     * @see ArrayIterator::getArrayCopy()
     */
    public function getArrayCopy(): array
    {
        $this->loadData();

        return $this->dataArrayIterator->getArrayCopy();
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::count()
     */
    public function count(): int
    {
        $this->loadData();

        return $this->dataArrayIterator->count();
    }

    /**
     * @see ArrayIterator::getFlags()
     */
    public function getFlags(): string
    {
        $this->loadData();

        return $this->dataArrayIterator->getFlags();
    }

    /**
     * @see ArrayIterator::setFlags()
     */
    public function setFlags(string $flags): void
    {
        $this->loadData();

        $this->dataArrayIterator->setFlags($flags);
    }

    /**
     * @see ArrayIterator::asort()
     */
    public function asort(int $flags = SORT_REGULAR): void
    {
        $this->loadData();

        $this->dataArrayIterator->asort($flags);
        $this->keyList = array_keys(
            iterator_to_array($this->dataArrayIterator)
        );
    }

    /**
     * @see ArrayIterator::ksort()
     */
    public function ksort(int $flags = SORT_REGULAR): void
    {
        $this->loadData();

        $this->dataArrayIterator->ksort($flags);
        $this->keyList = array_keys(
            iterator_to_array($this->dataArrayIterator)
        );
    }

    /**
     * @see ArrayIterator::uasort()
     * @noinspection SpellCheckingInspection
     */
    public function uasort(callable $callback): void
    {
        $this->loadData();

        $this->dataArrayIterator->uasort($callback);
        $this->keyList = array_keys(
            iterator_to_array($this->dataArrayIterator)
        );
    }

    /**
     * @see ArrayIterator::uksort()
     * @noinspection SpellCheckingInspection
     */
    public function uksort(callable $callback): void
    {
        $this->loadData();

        $this->dataArrayIterator->uksort($callback);
        $this->keyList = array_keys(
            iterator_to_array($this->dataArrayIterator)
        );
    }

    /**
     * @see ArrayIterator::natsort()
     */
    public function natsort(): void
    {
        $this->loadData();

        $this->dataArrayIterator->natsort();
        $this->keyList = array_keys(
            iterator_to_array($this->dataArrayIterator)
        );
    }

    /**
     * @see ArrayIterator::natcasesort()
     */
    public function natcasesort(): void
    {
        $this->loadData();

        $this->dataArrayIterator->natcasesort();
        $this->keyList = array_keys(
            iterator_to_array($this->dataArrayIterator)
        );
    }

    /**
     * @see ArrayIterator::unserialize()
     */
    public function unserialize(string $data): string
    {
        $this->loadData();

        return $this->dataArrayIterator->unserialize($data);
    }

    /**
     * @see ArrayIterator::serialize()
     */
    public function serialize(): string
    {
        $this->loadData();

        return $this->dataArrayIterator->serialize();
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::rewind()
     */
    public function rewind(): void
    {
        $this->loadData();

        $this->dataArrayIterator->rewind();
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::current()
     */
    public function current()
    {
        $this->loadData();

        return $this->dataArrayIterator->current();
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::key()
     */
    public function key()
    {
        $this->loadData();

        return $this->dataArrayIterator->key();
    }

    public function firstKey()
    {
        $this->loadData();

        reset($this->keyList);

        return key($this->keyList);
    }

    public function lastKey()
    {
        $this->loadData();

        return end($this->keyList);
    }

    public function keyList(): array
    {
        $this->loadData();

        return $this->keyList;
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::next()
     */
    public function next(): void
    {
        $this->loadData();

        $this->dataArrayIterator->next();
    }

    /**
     * @inheritdoc
     * @see ArrayIterator::valid()
     */
    public function valid(): bool
    {
        $this->loadData();

        return $this->dataArrayIterator->valid();
    }

    /**
     * @see ArrayIterator::seek()
     */
    public function seek(int $offset): void
    {
        $this->loadData();

        $this->dataArrayIterator->seek($offset);
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function loadData(bool $reload = false)
    {
        if ($this->dataArrayIterator === null || $reload) {
            $this->dataArrayIterator = new ArrayIterator();
            $this->keyList = [];

            if ($this->keyGenerator === null) {
                /** @var int|string $key */
                foreach ($this->getInnerIterator() as $key => $value) {
                    $this->dataArrayIterator->offsetSet($key, $value);
                    $this->keyList[$key] = $key;
                }
            } else {
                foreach ($this->getInnerIterator() as $key => $value) {
                    /** @var int|string $generatedKey */
                    $generatedKey = call_user_func($this->keyGenerator, $key, $value);

                    if (!is_scalar($key)) {
                        throw new CollectionException(
                            'Generated key must be scalar type, "'
                            . gettype($key)
                            . '" given for key: '
                            . $key
                        );
                    }

                    $this->dataArrayIterator->offsetSet($generatedKey, $value);
                    $this->keyList[$generatedKey] = $generatedKey;
                }
            }

            $this->dataArrayIterator->rewind();
        }

        return $this;
    }

    /**
     * @deprecated 2.0.0
     *
     * Handles (saves) the data of the iterator memory object
     *
     * @return void
     */
    protected function processData(): void
    {
        $this->loadData();
    }
}
