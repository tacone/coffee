<?php


namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\DictionaryTrait;

class DictionaryAttribute extends ArrayAttribute
{

    use DictionaryTrait;

    public function exposes()
    {
        return [
            'accessors' => ['has'],
            'others' => ['add', 'remove']
        ];
    }
}
