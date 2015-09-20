<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class EloquentCollectionDataSource extends AbstractEloquentDataSource
{
    public function read($key)
    {
        return $this->getDelegatedStorage()->get($key);
    }

    protected function write($key, $value)
    {
        // quite simply, if the field is a relation, we should not write
        // it, because we already did that earlier on

        if ($value instanceof Model) {
            return;
        };
        $this->getDelegatedStorage()->$key = $value;
    }

    protected function unsets($key)
    {
        if ($this->offsetExists($key)) {
            unset($this->getDelegatedStorage()->$key);
        }
    }
}
