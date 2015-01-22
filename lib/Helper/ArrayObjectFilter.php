<?php
namespace Tacone\Coffee\Helper;

use DeepCopy\Filter\Filter;
use ReflectionProperty;

class ArrayObjectFilter implements Filter
{
    /**
     * {@inheritdoc}
     */
    public function apply($object, $property, $objectCopier)
    {
        $reflectionProperty = new ReflectionProperty($object, $property);

        $reflectionProperty->setAccessible(true);
        $oldCollection = $reflectionProperty->getValue($object);

        $newCollection = $objectCopier($oldCollection);
        $oldItems = $newCollection->getArrayCopy();
        $newItems = $objectCopier($oldItems);
        $newCollection->exchangeArray($newItems);

        $reflectionProperty->setValue($object, $newCollection);
    }
}
