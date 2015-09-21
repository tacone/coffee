<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Model;

function jjj()
{
    dd('jjj');
}

class EloquentModelDataSource extends AbstractEloquentDataSource
{
    public function read($key)
    {
        $value = isset($this->getDelegatedStorage()->$key)
            ? $this->getDelegatedStorage()->$key
            : null;

        return $value;
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
