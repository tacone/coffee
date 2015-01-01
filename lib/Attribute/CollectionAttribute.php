<?php


namespace Tacone\Coffee\Attribute;

use Tacone\Coffee\Base\FluentCollectionTrait;

class CollectionAttribute extends ArrayAttribute
{

    use FluentCollectionTrait;

    public function exposes()
    {
        return [
            'accessors' => ['has'],
            'others' => ['add', 'remove']
        ];
    }
}
