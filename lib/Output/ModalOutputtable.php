<?php

namespace Tacone\Coffee\Output;

use Tacone\Coffee\Base\Exposeable;

class ModalOutputtable extends CompositeOutputtable
{
    protected $mode = null;

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    public function current()
    {
        if (!$this->mode) {
            throw new \LogicException('mode not selected');
        }
        if (!isset($this[$this->mode])) {
            throw new \RuntimeException("mode '{$this->mode}' does not exist");
        }

        return $this[$this->mode];
    }

    protected function render()
    {
        $mode = $this->current();

        return $mode->output();
    }
    public function __invoke()
    {
        return $this;
    }

    /**
     * Implements a jQuery-like interface.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        return Exposeable::handleExposeables($this, $method, $parameters);
    }
}
