<?php


namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\StringableTrait;

class ErrorsAttribute extends ArrayAttribute
{

    /**
     * Required by StringableTrait, must return a string;
     * @return string
     */
    protected function render()
    {
        if ($value = parent::render()) {
            return '<div class="text-danger">'.$value.'</div>';
        }

        return '';
    }

}
