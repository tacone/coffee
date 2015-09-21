<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use SplObjectStorage;

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

    public function log($l)
    {
        //        echo $l.PHP_EOL;
    }

    /**
     * @return SplObjectStorage
     */
    public function cache()
    {
        if (!static::$cache) {
            static::$cache = new SplObjectStorage();
        }

        return static::$cache;
    }

    /**
     * Returns the cached relations for a given
     * parent model.
     *
     * @param $model
     *
     * @return array
     */
    protected function relations(Model $model)
    {
        $cache = $this->cache();
        if (isset($cache[$model])) {
            return $cache[$model];
        }

        return [];
    }

    /**
     * This cache is made of strong and justice.
     *
     * We need to cache relations and models for each field we
     * read or change because Eloquent lazy loading actually
     * means that the developers have been lazy enough not
     * to give us a way to parse the relations of a model
     * before saving.
     *
     * Please note: this method writes a global (static) cache.
     *
     * @param string   $key      name of method on the main model
     *                           that returned the relation.
     * @param Relation $relation the relation object
     * @param Model    $model    the child model
     */
    protected function cacheRelation($key, Relation $relation, $model)
    {
        if (!$model instanceof Model) {
            throw new \LogicException('I can only cache Eloquent Models, instance of '.get_class($model).' given.');
        }
        $cache = $this->cache();
        if (!isset($cache[$this->getDelegatedStorage()])) {
            $cache[$this->getDelegatedStorage()] = [];
        }
        $cacheData = $cache[$this->getDelegatedStorage()];
        $cacheData[$key] = compact('model', 'relation');
        $cache[$this->getDelegatedStorage()] = $cacheData;
    }

    protected function relationMethodExists($key)
    {
        return method_exists($this->getDelegatedStorage(), $key);
    }

    protected function getRelationForKey($key)
    {
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
        if (!$this->relationMethodExists($key)) {
            // not a relation and not a method
            return;
        }

        $relation = $this->getRelationForKey($key);

        // if this is not a relation, then it's the actual value
        // returned by a method (like for example computed attributes)
        // so we just return it as the value
        if (!$relation instanceof Relation) {
            return $relation;
        }

        if (!$this->isSupportedRelation($relation)) {
            throw new \RuntimeException(
                'Unsupported relation '.get_class($relation)
                .' found in '.get_class($this->getDelegatedStorage())
                .'::'.$key);
        }

        // empty model, let's create one anew
        $model = $this->newModelFromRelation($key, $relation);
        if (is_null($model)) {
            throw new \LogicException(sprintf(
                'newModelFromRelation returned NULL (parent: %s, key: %s, relation: %s)',
                get_type_class($this->getDelegatedStorage()), $key, get_type_class($relation)
            ));
        }
        $relation = $this->getRelationForKey($key);

        if (
            $model instanceof Model
            || $model instanceof Collection
        ) {
            $this->cacheRelation($key, $relation, $model);

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
        }

        return $model;
    }

    /**
     * Creates a new model instance for a relation or returns
     * an existing one.
     * Recycling already created models is needed, otherwise
     * multiple fields targetting the same related model will
     * overwrite each other with empty values.
     *
     * @param string   $key
     * @param Relation $relation
     *
     * @return Model
     */
    protected function newModelFromRelation($key, Relation $relation)
    {
        // if you think those 2 issets can be simplified into just one
        // you've probably never dealt with \SplObjectStorage
        $cache = $this->cache();
        if (
            isset($cache[$this->getDelegatedStorage()])
            && isset($cache[$this->getDelegatedStorage()][$key]['model'])
        ) {
            if ($model = $cache[$this->getDelegatedStorage()][$key]['model']) {
                return $model;
            }
        }

        if (
            $relation instanceof BelongsToMany
        || $relation instanceof HasMany
        ) {
            $this->log($key);

            return $relation->getModel()->newCollection();
        }

        return $relation->getModel();
    }

    public function save()
    {
        //        return;

        $model = $this->unwrap();
        $modelRelations = $this->relations($model);

        // Cheat sheet
        //
        // - a male belongsTo a female
        // - each female hasOneOrMany males
        // - males have a $otherKey*, females don't and get associated
        //   by their $foreignKey (usually the primary key)
        // - females need a pivot table to associate among them
        //
        // * a $otherKey is something along the lines of  `author_id`
        //
        // +---------------+--------------+-------------+-------+---------+
        // | Name          | Type         | Key to use  | Sex*  | Save    |
        // +---------------+--------------+-------------+-------+---------+
        // | HasOne        | One-to-Many¹ | Primary²    |  F/M  | Before³ |
        // | HasMany       | One-to-Many  | Primary²    |  F/M  | Before³ |
        // | BelongsTo     | One-to-One   | Other Key   |  M/F  | Later   |
        // | BelongsToMany | Many-to-Many | Pivot Table |  F/F  | N/A¼    |
        // +---------------+--------------+-------------+-------+---------+
        // *: gender of the child model
        // ¹: one result will be returned, instead of a collection
        // ²: by default. It may be another key as well
        // ³; when using the primary key, as we don't know it's value yet
        // ¼: save both models, then write the pivot table
        //
        // a model can be male and female at the same time
        //
        // we need to save females first, then the current model,
        // then each male

        foreach ($modelRelations as $key => $mr) {
            $son = $mr['model'];
            $relation = $mr['relation'];

            if ($relation instanceof BelongsTo) {
                DataSource::make($son)->save();
//                $son->save();
                $relation->associate($son);
            }
        }
        $model->save();
        foreach ($modelRelations as $key => $model) {
            $daughter = $mr['model'];
            $relation = $mr['relation'];
            if ($relation instanceof HasOne) {
                // the following code is the same that would be executed with:
                // ```
                // $relation->save($daughter);
                // ```
                // we inline it here to properly wrap the save() method
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
        // quite simply, if the field is a relation, we should not write
        // it, because eloquent has already done it for us
//        if (isset($cache[$this->getDelegatedStorage()][$key]['model'])

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

    public function unwrap()
    {
        return $this->getDelegatedStorage();
    }

    protected function arrayize()
    {
        return $this->unwrap()->toArray();
    }

////  -- Not yet used
//
//    /**
//     * @return Relation
//     */
//    public function getParentRelation()
//    {
//        return $this->parentRelation;
//    }
//
//    /**
//     * @param Relation $parentRelation
//     */
//    public function setParentRelation($parentRelation)
//    {
//        $this->parentRelation = $parentRelation;
//
//        return $this;
//    }
}
