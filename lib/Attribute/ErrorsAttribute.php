<?php


namespace Tacone\Coffee\Attribute;

class ErrorsAttribute extends ArrayAttribute
{
    /**
     * Required by StringableTrait, must return a string;
     * @return string
     */
    protected function render()
    {
        if ($value = parent::render()) {
            return '<div class="text-danger">' . $value . '</div>';
        }

        return '';
    }

    public function __invoke()
    {
        // avoid parent-chaining
        if (!func_num_args()) {
            return clone($this);
        }
        $arguments = func_get_args();

        return call_user_func_array('parent::__invoke', $arguments);
    }
}
