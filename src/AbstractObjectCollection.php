<?php

/** @noinspection PhpUnusedParameterInspection */

namespace Wmsamolet\PhpObjectCollections;

use Wmsamolet\PhpObjectCollections\Exceptions\CollectionValidateException;

abstract class AbstractObjectCollection extends AbstractCollection
{
    abstract protected function collectionObjectClassName(): string;

    /**
     * @inheritdoc
     *
     * @param object $value
     *
     * @return bool
     */
    public function validate($value, $key = null, bool $throwException = false): bool
    {
        $isValid = true;
        $collectionObjectClassName = $this->collectionObjectClassName();

        if (!is_object($value)) {
            if ($throwException) {
                throw new CollectionValidateException(
                    'Collection item must be instance of object "' . $collectionObjectClassName . '": but "'
                    . gettype($value)
                    . '" given'
                );
            }

            $isValid = false;
        }

        if ($isValid) {
            $valueClassName = get_class($value);

            if ($valueClassName !== $collectionObjectClassName) {
                if ($throwException) {
                    throw new CollectionValidateException(
                        "Collection item value must be equal \"$collectionObjectClassName\" but \"$valueClassName\" given"
                    );
                }

                $isValid = false;
            }
        }

        return $isValid;
    }
}
