<?php


namespace Tacone\Coffee\Base;

trait StringableTrait
{
    private $stringableTraitOutput = true;

    public function __toString()
    {
        try {
            $value = $this->output();
            if (!is_string($value)) {
                // we must throw an exception manually here because if $value
                // is not a string, PHP will trigger an error right after the
                // return statement, thus escaping our try/catch.
                throw new \LogicException(__CLASS__."::__toString() must return a string");
            }

            return $value;
        } catch (\Exception $exception) {
            $previousErrorHandler = set_exception_handler(function () {
            });
            restore_error_handler();
            call_user_func($previousErrorHandler, $exception);
            die;
        }
    }

    public function output($show = null)
    {
        if (func_num_args()) {
            if (is_bool($show)) {
                $this->stringableTraitOutput = $show;

                return $this;
            }
        }
        if (!$this->stringableTraitOutput) {
            return '';
        }
        if ($this->stringableTraitOutput instanceof \Closure) {
            $func = $this->stringableTraitOutput;

            return $func($this);
        }

        return $this->render();
    }
}
