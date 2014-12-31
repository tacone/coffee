<?php
namespace Tacone\Coffee\Base;

/**
 * Use this trait to enable objects to expose a jQuery-like
 * interface from their parent objects.
 *
 * Override the exposes() method to tell the parent which methods to
 * expose as accessors, and which to expose as accessors or fluent methods.
 *
 * The parent object will then need to call the handleExposeables()
 * static method to delegate the method call to their exposeable
 * children objects.
 *
 * Class Exposeable
 * @package Tacone\Coffee\Base
 */
trait Exposeable
{
    /**
     * The methods exposed by this property
     * @return array
     */
    public function exposes()
    {
        /**
         * For example:
         *
         * <pre>
         * return [
         *     'accessors' => ['has'],
         *     'others' => ['add', 'remove']
         * ];
         * </pre>
         */

        return [];
    }

    /**
     * Whether a property instance is exposeable or not.
     *
     * @param $attribute
     * @return bool
     */
    public static function isExposeable($attribute)
    {
        if (!is_object($attribute)) {
            return false;
        }
        $traits = class_uses_recursive(get_class($attribute));

        return in_array(__TRAIT__, $traits);

    }

    /**
     * Calls a property method and returns the result or the parent itself.
     *
     * @param  object $parent     the parent object
     * @param  object $property   the chosen parent property
     * @param  string $method     the name of the method to call
     * @param  array  $parameters the arguments to pass to the method
     * @param  null   $return     weather to return the result or the parent object
     * @return mixed  $result the result returned by the method or the $parent itself
     */
    public static function callExposeableMethod($parent, $property, $method = null, $parameters, $return = null)
    {
        $callback = $method ? [$property, $method] : $property;
        $result = call_user_func_array($callback, $parameters);
        if (is_bool($return)) {
            return $return ? $result : $parent;
        }

        if ($return === null) {
            return $result === $property ? $parent : $result;
        }
        throw new \LogicException('$return can be either null/true/false, ' . gettype($return) . ' given');
    }

    /**
     * Cycles $parent and find the applicable exposeable property to call the
     * requested method on.
     * The matching is based on a combination of the property name and the
     * attribute name to be called.
     *
     * For example: $parent->removeAttr() may match $parent->attr->remove();
     * If the attribute implements the __invoke() magic method, then it may
     * be called directly, ex: $parent->attr();
     *
     * @param  object $parent     the object instance that contains the attributes
     * @param  string $methodName the requested method (i.e.: removeAttr)
     * @param  array  $parameters the arguments to pass to the method
     * @return mixed  $result the result returned by the method or the $parent itself
     */
    public static function handleExposeables(
        $parent,
        $methodName,
        $parameters
    ) {
        $parentProperties = array_keys(get_object_vars($parent));
        $parentProperties = array_filter($parentProperties, function ($prop) use ($methodName, $parent) {
            return stripos($methodName, $prop) !== false
            && is_object($parent->$prop)
            && static::isExposeable($parent->$prop);
        });;

        foreach ($parentProperties as $propertyName) {
            // check if the property implements the __invoke() method
            if ($methodName === $propertyName && is_callable($parent->$propertyName)) {
                return static::callExposeableMethod($parent, $parent->$methodName, null, $parameters);
            }

            // retrieve the exposed methods
            $propertyMethods = $parent->$propertyName->exposes();
            $accessors = isset($propertyMethods['accessors']) ? (array) $propertyMethods['accessors'] : [];
            $others = isset($propertyMethods['others']) ? (array) $propertyMethods['others'] : [];

            // check if any accessor applies
            foreach ($accessors as $method) {
                if ($methodName === $method . ucfirst($propertyName)) {
                    return static::callExposeableMethod($parent, $parent->$propertyName, $method, $parameters, false);
                }
            }

            // check the other methods
            foreach ($others as $method) {
                if ($methodName === $method . ucfirst($propertyName)) {
                    return static::callExposeableMethod($parent, $parent->$propertyName, $method, $parameters, false);
                }
            }
        }

        // the method does not exists or it's been not exposed
        throw new \RuntimeException('Method \'' . get_class($parent) . "::$methodName' does not exists'");
    }
}
