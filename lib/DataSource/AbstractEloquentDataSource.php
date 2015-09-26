<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Collection;
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

    protected function getMethodForKey($key)
    {
        // see the source of Eloquent\Model::getRelationshipFromMethod()
        $camelKey = camel_case($key);

        return method_exists($this->getDelegatedStorage(), $camelKey) ? $key : null;
    }

    protected function getValueOrRelationForKey($key)
    {

        // not a relation nor a method
        if (!$camelKey = $this->getMethodForKey($key)) {
            return;
        }

        return $this->getDelegatedStorage()->$camelKey();
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

        // TODO we need to bind the relation to the datasource here
        $model = Rel::make($relation)->getChild();

        if (!$this->isEloquentObject($model)) {
            throw new \LogicException(sprintf(
                'newModelFromRelation should return an Eloquent Model/Collection, but returned a %s (parent: %s, key: %s, relation: %s)',
                get_type_class($model),
                get_type_class($this->getDelegatedStorage()), $key, get_type_class($relation)
            ));
        }

        return $model;
    }

    protected function isEloquentObject($object)
    {
        return is_eloquent_object($object);
    }

    public function cacheAndAssociate($key, $modelOrCollection, $relation = null)
    {
        if (func_num_args() < 3) {
            $relation = Cache::getRelation($this->getDelegatedStorage(), $key) ?: $this->getValueOrRelationForKey($key);
            if (!$relation instanceof Relation) {
                throw new \LogicException('Relation expected, got '.get_type_class($relation));
            }
            $this->supportedRelationOrThrow($key, $relation);
        }

        // we set the relation for later use (saving)

        Cache::set($this->getDelegatedStorage(), $key, $relation, $modelOrCollection);

        // and we associate the child model with it

        if ($modelOrCollection instanceof Model) {
            $modelOrCollection = [$modelOrCollection];
        }
        foreach ($modelOrCollection as $model) {
            Rel::make($relation)->associate($key, $model);
        }
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
