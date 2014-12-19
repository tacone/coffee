<?php


namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\DelegatedArrayTrait;
use Tacone\Coffee\Base\StringableTrait;

class ErrorsAttribute extends ArrayAttribute
{

    /**
     * Required by StringableTrait, must return a string;
     * @return string
     */
    public function output()
    {
        if ($value = parent::output())
        {
            return '<div class="text-danger">'.$value.'</div>';
        }
        return '';
    }


}