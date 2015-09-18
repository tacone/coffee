<?php

namespace Tacone\Coffee\Base;

trait StringableTrait
{
    private $stringableTraitOutput = true;

    /**
     * Casts the object to string, by the means of the output() method.
     *
     * Since in PHP you cannot throw exception from the __toString() method
     * we catch whatever exception we can and subsequently call the
     * exception manually, to preserve the disaster-handling logic of the app
     * and possibly provide useful stacktraces for debugging.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $value = $this->output();
            if (!is_string($value)) {
                // we must throw an exception manually here because if $value
                // is not a string, PHP will trigger an error right after the
                // return statement, thus escaping our try/catch.
                throw new \LogicException(__CLASS__.'::__toString() must return a string');
            }

            return $value;
        } catch (\Exception $exception) {
            // the only way to get the current exception handler in PHP is by
            // the means of setting a new one while storing the return value
            // of set_exception_handler. So we push an empty one, just to read
            // the previous. Then we immediately restore it, just in case.
            $previousErrorHandler = set_exception_handler(function () {
            });
            restore_error_handler();

            // now we can call the exception handler manually. Think about it
            // as a sort of GOTO.
            call_user_func($previousErrorHandler, $exception);

            // if the exception handler did not end the execution already we
            // will force it, just in case, to prevent any leak of debugging
            // output onto the end user.
            die;
        }
    }

    /**
     * This method handles the logic of converting the object to a string.
     *
     * When called without arguments, it MUST return a string. If you pass
     * an argument, it SHOULD behave as a mutator method, changing the state
     * of the output logic and return $this for chaining purposes.
     *
     * If $show === false, the output will be suppressed from now on (empty
     * string)
     * If $show === true, the output will be enabled back again
     * If $show instanceof Closure, the output will be the return value of
     * the closure.
     *
     * @param bool|\Closure $show optional argument
     *
     * @return $this|string
     */
    public function output($show = null)
    {
        if (func_num_args()) {
            if (is_bool($show)) {
                $this->stringableTraitOutput = $show;
            }
            if ($show instanceof \Closure) {
                $this->stringableTraitOutput = $show;
            }

            return $this;
        }
        if (!$this->stringableTraitOutput) {
            return '';
        }
        if ($this->stringableTraitOutput instanceof \Closure) {
            $func = $this->stringableTraitOutput;

            $result = $func($this);
            if ($result === 'false') {
                return '';
            }
            // null means $this for us | avoid infinite loop
            if (!is_null($result) && $result !== $this) {
                return (string) $result;
            }
        }

        $result = $this->render();
        if (is_array($result)) {
            $result = implode('', $result);
        }

        return $result;
    }
}
