<?php

namespace Wmsamolet\PhpObjectCollections;

use ArrayAccess;
use Countable;
use Iterator;
use Traversable;

interface CollectionInterface extends Iterator, ArrayAccess, Countable, ArrayableInterface
{
    public function getIterator(): ArrayIteratorIterator;

    /**
     * Sets the iterator data collection
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @param Iterator $iterator
     *
     * @return static
     */
    public function setIterator(Traversable $iterator);

    /**
     * Setting the callback function to be called when the count of the number of items in the collection
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @param callable $callback
     *
     * @return static
     */
    public function setCountCallback(callable $callback);

    /**
     * Get the callback function to be called when the count of the number of items in the collection
     */
    public function getCountCallback(): ?callable;

    /**
     * Verifies conformance of the key and item in the collection
     *
     * @param $value
     * @param null|int|string $key
     * @param bool $throwException
     *
     * @return bool
     */
    public function validate($value, $key = null, bool $throwException = false): bool;

    /**
     * Get current offset position
     */
    public function offset(): int;

    /**
     * Get collection item key by offset
     */
    public function offsetKey(int $offset): ?int;

    /**
     * Get list all keys
     *
     * @return int[]|string[]
     */
    public function keyList(): array;

    /**
     * Get first collection item key
     *
     * @return null|int|string
     */
    public function firstKey();

    /**
     * Get first collection item
     */
    public function first();

    /**
     * Get last collection item key
     *
     * @return null|int|string
     */
    public function lastKey();

    /**
     * Get last collection item
     *
     * @return mixed
     */
    public function last();

    /**
     * Add the item to the collection for the specified key
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @param mixed $value Collection item
     *
     * @return static
     */
    public function add($value);

    /**
     * Add the list of items to collection
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function addList(array $items);

    /**
     * Sets the item to the collection for the specified key
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @param null|int|string|true $key Collection item key
     * @param mixed $value Collection item
     *
     * @return static
     */
    public function set($key, $value);

    /**
     * Sets the list of items in the collection
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function setList(array $items);

    /**
     * Checks the existence of a key in the collection
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key): bool;

    /**
     * Gets the collection item by key
     *
     * @param $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Gets the collection item by key
     *
     * @param int $offset
     *
     * @return mixed
     */
    public function getByOffset(int $offset);

    /**
     * Gets all the items in the collection
     */
    public function getList(): array;

    /**
     * Removes the item from the collection based on its key
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @param int|string $key
     *
     * @return static
     */
    public function remove($key);

    /**
     * Remove all items from collection
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function removeAll();

    /**
     * Get items batch from collection
     *
     * @param int $size Number of items in a batch
     *
     * @return iterable
     */
    public function batch(int $size): iterable;

    /**
     * @noinspection PhpMissingParamTypeInspection
     *
     * @param callable $keyOrValue
     * @param callable|null $value
     * @param callable|null $group
     *
     * @return array
     */
    public function map($keyOrValue, $value = null, $group = null): array;

    /**
     * Filter collection
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function filter(callable $filterCallback);

    /**
     * Sort collection
     *
     * @noinspection PhpMissingReturnTypeInspection
     *
     * @return static
     */
    public function sort(callable $callback, bool $byKey = false);
}
