<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EloquentModelDataSource extends AbstractEloquentDataSource
{
    public function read($key)
    {
        return $this->getDelegatedStorage()->$key;
    }

    protected function write($key, $value)
    {
        // if it's a model/collection, we already wrote it
        // also eloquent would store the new model inside the
        // attributes, which would make saves fail

        if ($this->isEloquentObject($value)) {
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
