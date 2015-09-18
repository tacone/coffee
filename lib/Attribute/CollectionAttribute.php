<?php

namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\CollectionTrait;

class CollectionAttribute extends ArrayAttribute
{
    use CollectionTrait;

    public function exposes()
    {
        return [
            'accessors' => ['has'],
            'others' => ['add', 'remove'],
        ];
    }
}
