<?php

namespace Tacone\Coffee\Base;

trait CompositeTrait
{
    /**
     * Override this function to return an associative
     * array with all the children.
     *
     * The keys will be the same used for the composite
     * method calls.
     */
    protected function compositeTraitGetChildren()
    {
        throw new \LogicException('Override this method');
    }

    public function __call($method, $arguments)
    {
        return $this->dispatch($method, $arguments);
    }
    protected function dispatch($method, $arguments = [])
    {
        $result = [];
        foreach ($this->compositeTraitGetChildren() as $key => $child) {
            $result[$key] = call_user_func_array([$child, $method], $arguments);
        }

        return $result;
    }
}
