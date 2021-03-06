<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model;

/**
 * Base metadata class with reusable merging ability.
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class AbstractMetadata implements MetadataInterface
{
    /**
     * @return string[]
     */
    protected function getNonMergeProperties()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MetadataInterface $metadata)
    {
        if (!$metadata instanceof $this || !$this instanceof $metadata) {
            // Because this merging is recursive, there could be child subjects of different types
            // If they're incompatible just return to allow merging to continue throughout other levels
            return;
        }

        $nonMergeProperties = $this->getNonMergeProperties();

        $inheritedVariables = get_object_vars($metadata);
        foreach ($inheritedVariables as $inheritedKey => $inheritedValue) {
            if ($this->{$inheritedKey} instanceof MetadataInterface) {
                $this->{$inheritedKey}->merge($inheritedValue);

                continue;
            }

            if (in_array($inheritedKey, $nonMergeProperties)) {
                continue;
            }

            if (null === $this->{$inheritedKey} || (is_array($this->{$inheritedKey}) && (0 === count($this->{$inheritedKey})))) {
                $this->{$inheritedKey} = $inheritedValue;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_map(
            function ($value) {
                if ($value instanceof MetadataInterface) {
                    $value = $value->toArray();
                }

                return $value;
            },
            get_object_vars($this)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function forAll(callable $callable)
    {
        foreach (get_object_vars($this) as $key => $value) {
            $this->$key = $callable($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $inheritedVariables = get_object_vars($this);

        foreach ($inheritedVariables as $inheritedKey => $inheritedValue) {
            if ($this->{$inheritedKey} instanceof MetadataInterface) {
                if (!$this->{$inheritedKey}->isEmpty()) {
                    return false;
                }

                continue;
            }

            if (!empty($inheritedValue)) {
                // If we have a value, is it the same as the class default? If so consider this as empty,
                // it's not a customised value (e.g. UTF-8 on charset)
                if (!isset($reflectionClass)) {
                    $reflectionClass = new \ReflectionClass($this);
                    $defaultProperties = $reflectionClass->getDefaultProperties();
                }
                if (isset($defaultProperties[$inheritedKey]) && $defaultProperties[$inheritedKey] == $inheritedValue) {
                    continue;
                }

                return false;
            }
        }

        return true;
    }
}
