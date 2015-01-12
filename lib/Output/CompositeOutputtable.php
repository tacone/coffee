<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Attribute\ArrayAttribute;
use Tacone\Coffee\Base\DictionaryTrait;
use Tacone\Coffee\Base\EntriesAsPropsTrait;
use Tacone\Coffee\Base\Exposeable;
use Tacone\Coffee\Base\StringableTrait;

class CompositeOutputtable extends ArrayAttribute
{
    use StringableTrait;
    use DictionaryTrait;
    use EntriesAsPropsTrait;

    public function __construct($outputtables = null)
    {
        $this->value = new \ArrayObject();
        if (is_array($outputtables) || $outputtables instanceof \Traversable) {
            foreach ($outputtables as $key => $object) {
                $this[$key] = $object;
            }
        }
    }

    protected function render()
    {
        return join('', $this->toArray());
    }

    /**
     * Implements a jQuery-like interface
     *
     * @param  string $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        return Exposeable::handleExposeables($this, $method, $parameters);
    }
}
