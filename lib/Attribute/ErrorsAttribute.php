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

}
