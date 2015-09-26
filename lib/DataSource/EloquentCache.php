<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use SplObjectStorage;

/**
 * This cache is made of strong and justice.
 *
 * We need to cache relations and models for each field we
 * read or change because Eloquent lazy loading actually
 * means that the developers have been lazy enough not
 * to give us a way to parse the relations of a model
 * before saving.
 *
 * Please note: this is a global (static) cache.
 *
 * @return SplObjectStorage
 */
class EloquentCache
{
    /**
     * @var SplObjectStorage
     */
    public static $cache;

    /**
     * @return SplObjectStorage
     */
    protected static function getCache()
    {
        if (!static::$cache) {
            static::$cache = new SplObjectStorage();
        }

        return static::$cache;
    }

    /**
     * @param Collection|Model $parent
     * @param string           $key
     *
     * @return array
     */
    public static function get($parent, $key)
    {
        is_eloquent_object($parent, true);
        // if you think those 2 issets can be simplified into just one
        // you've probably never dealt with \SplObjectStorage
        $cache = static::getCache();
        if (
            isset($cache[$parent])
            && isset($cache[$parent][$key])
        ) {
            return $cache[$parent][$key];
        }
    }

    /**
     * @param Collection|Model $parent
     * @param string           $key
     *
     * @return Collection|Model
     */
    public static function getChild($parent, $key)
    {
        is_eloquent_object($parent, true);

        return static::get($parent, $key)['child'];
    }

    /**
     * @param Collection|Model $parent
     * @param string           $key
     *
     * @return Relation
     */
    public static function getRelation($parent, $key)
    {
        is_eloquent_object($parent, true);

        return static::get($parent, $key)['relation'];
    }

    /**
     * Returns the cached relations for a given
     * parent model.
     *
     * @param $model
     *
     * @return array
     */
    public static function all($parent)
    {
        is_eloquent_object($parent, true);
        $cache = static::getCache();
        if (isset($cache[$parent])) {
            return $cache[$parent];
        }

        return [];
    }

    /**
     * @param Collection|Model the parent object
     * @param string           $key      name of method on the main model
     *                                   that returned the relation.
     * @param Relation         $relation the relation object
     * @param Collection|Model $child    the child model
     */
    public static function set($parent, $key, Relation $relation, $child)
    {
        is_eloquent_object($parent, true);

        $cache = static::getCache();
        if (!isset($cache[$parent])) {
            $cache[$parent] = [];
        }
        $cacheData = $cache[$parent];
        $cacheData[$key] = compact('child', 'relation');
        $cache[$parent] = $cacheData;
    }
}
