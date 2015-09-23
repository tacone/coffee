<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class EloquentCollectionDataSource extends AbstractEloquentDataSource
{
    protected $parentRelation;

    public function __construct(Collection $collection)
    {
        parent::__construct($collection);
    }

    public function bindToModel(Model $model)
    {
        $this->parentRelation = $model;

        return $this;
    }

    public function bindToRelation(Relation $relation)
    {
        $this->parentRelation = $relation;

        return $this;
    }

    public function read($key)
    {
        return $this->getDelegatedStorage()->get($key);
    }

    protected function write($key, $value)
    {
        // we don't write down models because we already did.

        if (!$value instanceof Model) {
            throw new \LogicException(sprintf(
                'You can only write models in DataSourceCollection  (parent: %s, key: %s, value: %s)',
                get_type_class($this->getDelegatedStorage()), $key, get_type_class($value)
            ));
        };
        $this->getDelegatedStorage()[$key] = $value;
    }
    protected function unsets($key)
    {
        if ($this->offsetExists($key)) {
            unset($this->getDelegatedStorage()->$key);
        }
    }

    protected function getValueOrRelationForKey($key)
    {
        if (!$this->parentRelation) {
            throw new \RuntimeException(
                'You have to bind a Model or Relation to this DataSource '
                .'before you can write it.'
            );
        }
        // both Relation and Model use an internal QueryBuilder
        return $this->parentRelation->getModel();
    }
}
