<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Model;

class EloquentModelDataSource extends AbstractEloquentDataSource
{
    public function read($key)
    {
        return$this->getDelegatedStorage()->$key;
    }

    protected function write($key, $value)
    {
        // we don't write down models because we already did.

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
