<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Tacone\Coffee\DataSource\EloquentCache as Cache;

abstract class AbstractEloquentDataSource extends AbstractDataSource
{
    protected static $cache;

    public function __construct($var)
    {
        if (!is_object($var)) {
            throw new \RuntimeException(
                'Argument 1 passed to '.get_class($this)
                .' must be an object, var of type '.gettype($var).' given'
            );
        }
        $this->storage = $var;
    }

    protected function methodForKeyExists($key)
    {
        return method_exists($this->getDelegatedStorage(), $key);
    }

    protected function getRelationForKey($key)
    {
        // not a relation nor a method
        if (!$this->methodForKeyExists($key)) {
            return;
        }

        return $this->getDelegatedStorage()->$key();
    }

    /**
     * Checks if the passed relation is supported.
     *
     * @param $relation
     *
     * @return bool
     */
    protected function isSupportedRelation(Relation $relation)
    {
        switch (true) {
            case $relation instanceof HasOne:
            case $relation instanceof BelongsTo:
            case $relation instanceof BelongsToMany:
            case $relation instanceof HasMany:
                return true;
        }

        return false;
    }

    /**
     * Caches a relation for later use.
     * Creates a new model in case of empty values.
     *
     * We need to invoke this method for every field
     * we want to save later, no exception
     *
     * @param $key
     * @param $model
     *
     * @return mixed
     */
    protected function createChild($key)
    {
        $relation = $this->getRelationForKey($key);

        // if this is not a relation, then it's the actual value
        // returned by a method (like for example computed attributes)
        // so we just return it as the value

        if (!$relation instanceof Relation) {
            return $relation;
        }

        $this->supportedRelationOrThrow($key, $relation);

        // empty model, let's create one anew
        $model = $this->newModelFromRelation($key, $relation);

        if (!(
            $model instanceof Model
            || $model instanceof Collection
        )
        ) {
            throw new \LogicException(sprintf(
                'newModelFromRelation returned NULL (parent: %s, key: %s, relation: %s)',
                get_type_class($this->getDelegatedStorage()), $key, get_type_class($relation)
            ));
        }

        Cache::set($this->getDelegatedStorage(), $key, $relation, $model);

        switch (true) {
            case $relation instanceof \Illuminate\Database\Eloquent\Relations\HasOne;
                $this->getDelegatedStorage()->setRelation($key, $model);
                break;
            case $relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo;
                $relation->associate($model);
                break;
            case $relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany;
                dd('break!');
                break;
            case $relation instanceof \Illuminate\Database\Eloquent\Relations\HasMany;
                dd('break!');
                break;
        }

        return $model;
    }

    /**
     * Creates a new model instance for a relation or returns
     * an existing one.
     * Recycling already created models is needed, otherwise
     * multiple fields targeting the same related model will
     * overwrite each other with empty values.
     *
     * @param string   $key
     * @param Relation $relation
     *
     * @return Model
     */
    protected function newModelFromRelation($key, Relation $relation)
    {
        if ($object = Cache::get($this->getDelegatedStorage(), $key)) {
            return $object['child'];
        }
        if (
            $relation instanceof BelongsToMany
            || $relation instanceof HasMany
        ) {
            return $relation->getModel()->newCollection();
        }

        return $relation->getModel();
    }

    // +---------------+--------------+-------------+-------+------------+
    // | Name          | Type         | Key to use  |Gender*| Save Child |
    // +---------------+--------------+-------------+-------+------------+
    // | HasOne        | One-to-Many¹ | Primary²    |  F/M  | Before³    |
    // | HasMany       | One-to-Many  | Primary²    |  F/M  | Before³    |
    // | BelongsTo     | One-to-One   | Other Key   |  M/F  | Later      |
    // | BelongsToMany | Many-to-Many | Pivot Table |  F/F  | N/A¼       |
    // +---------------+--------------+-------------+-------+------------+

    public function save()
    {
        $model = $this->unwrap();
        $modelRelations = Cache::all($model);

        foreach ($modelRelations as $key => $mr) {
            $son = $mr['child'];
            $relation = $mr['relation'];

            if ($relation instanceof BelongsTo) {
                DataSource::make($son)->save();
                $relation->associate($son);
            }
        }
        $model->save();

        foreach ($modelRelations as $key => $model) {
            $daughter = $mr['child'];
            $relation = $mr['relation'];
            if ($relation instanceof HasOne) {
                // this is what `$relation->save($daughter)` does
                // we inline it here to wrap the save() method
                $daughter->setAttribute($relation->getPlainForeignKey(), $relation->getParentKey());
                DataSource::make($daughter)->save();
            }
        }
    }

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

    protected function arrayize()
    {
        return $this->unwrap()->toArray();
    }

    /**
     * @param $key
     * @param $relation
     */
    protected function supportedRelationOrThrow($key, Relation $relation)
    {
        if (!$this->isSupportedRelation($relation)) {
            throw new \RuntimeException(
                'Unsupported relation '.get_class($relation)
                .' found in '.get_class($this->getDelegatedStorage())
                .'::'.$key);
        }
    }
}
