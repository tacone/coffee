<?php
namespace Tacone\Coffee\Base;

trait Exposeable
{
    public function expose()
    {
        return [];
    }

    public static function isExposeable($object)
    {
        if (!is_object($object)) {
            return false;
        }
        $traits = class_uses_recursive(get_class($object));

        return in_array(__TRAIT__, $traits);

    }

    public static function callExposeableMethod($parent, $object, $method = null, $parameters, $return = null)
    {
        $callback = $method ? [$object, $method] : $object;
        $result = call_user_func_array($callback, $parameters);
        if (is_bool($return)) {
            return $return ? $result : $parent;
        }

        if ($return === null) {
            return $result === $object ? $parent : $result;
        }
        throw new \LogicException('$return can be either null/true/false, ' . gettype($return) . ' given');
    }

    public static function handleExposeables(
        $parent,
        $methodName,
        $parameters
    ) {
        $properties = array_keys(get_object_vars($parent));
        $properties = array_filter($properties, function ($prop) use ($methodName, $parent) {
            return stripos($methodName, $prop) !== false
            && is_object($parent->$prop)
            && static::isExposeable($parent->$prop);
        });;

        foreach ($properties as $prop) {
            if ($methodName === $prop && is_callable($parent->$prop)) {
                return static::callExposeableMethod($parent, $parent->$methodName, null, $parameters);
            }
            $exposeds = $parent->$prop->exposes();
            $accessors = isset($exposeds['accessors']) ? (array) $exposeds['accessors'] : [];
            $others = isset($exposeds['others']) ? (array) $exposeds['others'] : [];

            foreach ($accessors as $method) {
                if ($methodName === $method . ucfirst($prop)) {
                    return static::callExposeableMethod($parent, $parent->$prop, $method, $parameters, false);
                }
            }
            foreach ($others as $method) {
                if ($methodName === $method . ucfirst($prop)) {
                    return static::callExposeableMethod($parent, $parent->$prop, $method, $parameters, false);
                }
            }
        }

        throw new \RuntimeException('Method \''.get_class($parent)."::$methodName' does not exists'");
    }
}
