<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class EloquentCollectionDataSource extends AbstractEloquentDataSource
{
    public function __construct(Collection $collection, Relation $relation = null)
    {
        $this->parentRelation = $relation;
        parent::__construct($collection);
    }

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
        $this->getDelegatedStorage()->set($value);
    }

    protected function unsets($key)
    {
        if ($this->offsetExists($key)) {
            unset($this->getDelegatedStorage()->$key);
        }
    }

    protected function getRelationForKey($key)
    {
        return $this->getDelegatedStorage()->$key();
    }
}
