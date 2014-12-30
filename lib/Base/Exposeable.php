<?php
namespace Tacone\Coffee\Base;


trait Exposeable
{
    public function expose(){
        return [];
    }

    static public function isExposeable($object)
    {
        if (!is_object($object)) {
            return false;
        }
        $traits = class_uses_recursive(get_class($object));
        return in_array(__TRAIT__, $traits);

    }

    static public function  callExposeableMethod($parent, $object, $parameters, $return = null)
    {
        $result = call_user_func_array($object, $parameters);
        if (is_bool($return)) {
            return $return ? $result : $parent;
        }

        if ($return === null) {
            return $result === $object ? $parent : $result;
        }
        throw new \LogicException('$return can be either null/true/false, ' . gettype($return) . ' given');
    }

    static public function handleExposeables(
        $parent,
        $methodName,
        $parameters
    ) {
        $properties = array_keys(get_object_vars($parent));
        $properties = array_filter($properties, function ($prop) use ($methodName, $parent) {
            return strpos($prop, $methodName) === 0
            && is_object($parent->$prop)
            && static::isExposeable($parent->$prop);
        });;

        foreach ($properties as $prop) {
//            var_dump($prop); die;
            if (is_callable($parent->$prop)) {
                return static::callExposeableMethod($parent, $parent->$prop, $parameters);
            }
            $exposeds = $parent->$prop->exposes();
            $accessors = isset($exposeds['accessors'])? (array)$exposeds['accessors']:[];
            $others = isset($exposeds['others'])? (array)$exposeds['others']:[];
            foreach ($accessors as $method) {
                if ($methodName === $method . ucfirst($prop)) {
                    return static::callExposeableMethod($parent, $parent->$prop, $parameters, false);
                }
            }
            foreach ($others as $method) {
                if ($methodName === $method . ucfirst($prop)) {
                    return static::callExposeableMethod($parent, $parent->$prop, $parameters, false);
                }
            }
        }

        throw new \RuntimeException('Method \''.get_class($parent)."::$methodName' does not exists'");
    }
}