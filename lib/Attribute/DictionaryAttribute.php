<?php

namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\DictionaryTrait;

class DictionaryAttribute extends ArrayAttribute
{
    use DictionaryTrait;

    public function exposes()
    {
        return [
            'accessors' => ['has', 'get'],
            'others' => ['add', 'remove'],
        ];
    }

    public function __invoke($key = null, $value = null)
    {
        $arguments = func_get_args();
        switch (count($arguments)) {
            case 0:
                throw new \InvalidArgumentException(__CLASS__.' expects at least one argument');
            case 1:
                if (\Str::startsWith($key, '!')) {
                    if (isset($this[substr($key, 1)])) {
                        unset($this[substr($key, 1)]);
                    }

                    return $this;
                }

                return isset($this[$key]) ? $this[$key] : null;
            case 2:
                $this[$key] = $value;

                return $this;
        }

        throw new \InvalidArgumentException(__CLASS__.' expects at most 2 arguments');
    }
}
