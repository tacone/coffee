<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Tacone\Coffee\DataSource\EloquentCache as Cache;
use Tacone\Coffee\DataSource\RelationApi as Rel;

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

    protected function getValueOrRelationForKey($key)
    {
        // not a relation nor a method
        if (!$this->methodForKeyExists($key)) {
            return;
        }

        return $this->getDelegatedStorage()->$key();
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
        $relation = $this->getValueOrRelationForKey($key);

        // if this is not a relation, then it's the actual value
        // returned by a method (like for example computed attributes)
        // so we just return it as the value

        if (!$relation instanceof Relation) {
            return $relation;
        }

        $this->supportedRelationOrThrow($key, $relation);

        // empty model, let's create one anew
        $model = Cache::getChild($this->getDelegatedStorage(), $key) ?: Rel::make($relation)->getChild();
        // TODO  ^^^^^^^^^^^^^^^^^^^^^^^^ something fishy here. Test the cache

        if (!$model instanceof Model && !$model instanceof Collection) {
            throw new \LogicException(sprintf(
                'newModelFromRelation returned an invalid result (parent: %s, key: %s, relation: %s)',
                get_type_class($this->getDelegatedStorage()), $key, get_type_class($relation)
            ));
        }

        Cache::set($this->getDelegatedStorage(), $key, $relation, $model);

        Rel::make($relation)->associate($key, $model);

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
            $relation = Rel::make($mr['relation']);

            if ($relation->canSaveBefore()) {
                $relation->saveBefore($mr['child']);
            }
        }
        $model->save();

        foreach ($modelRelations as $key => $model) {
            $relation = Rel::make($mr['relation']);

            if ($relation->canSaveAfter()) {
                $relation->saveAfter($mr['child']);
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
        //        if (!$this->isSupportedRelation($relation)) {
        if (!Rel::isSupported($relation)) {
            throw new \RuntimeException(
                'Unsupported relation '.get_class($relation)
                .' found in '.get_class($this->getDelegatedStorage())
                .'::'.$key);
        }
    }
}
