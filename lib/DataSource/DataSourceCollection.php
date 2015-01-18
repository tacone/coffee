<?php


namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * You neighbourly dot syntax data source.
 *
 * This class has been built to abstract away the horrible mess that
 * Eloquent is, internally.
 *
 * Usage:
 *  <pre>
 *      $datasource = new DataSource(new Article());
 *      $name = $datasource['author.name'];
 *      $datasource['author.name'] = 'new name';
 *      $datasource->save();
 *  </pre>
 *
 * You can get and set values using dot syntax. The class handles all
 * the complexities of finding fields, of instantiating a new model if the
 * relation is empty, and of saving all the bunch in the right order.
 *
 * No change to your models is needed.
 */
class DataSourceCollection extends DataSource
{
    protected function isSupportedRelation(Relation $relation)
    {
        if (parent::isSupportedRelation($relation)) {
            return true;
        }
        if ($relation instanceof BelongsToMany) {
            return true;
        }

        return false;
    }
    protected function cacheRelation($key, Relation $relation, $model)
    {
        die('collection!');
        if (!$model instanceof Collection) {
            throw new \LogicException("I can only cache Eloquent Collections, instance of ".get_class($model)." given.");
        }
        $cache = $this->cache();
        if (!isset($cache[$this->source])) {
            $cache[$this->source] = [];
        }
        $cacheData = $cache[$this->source];
        $cacheData[$key] = compact('model', 'relation');
        $cache[$this->source] = $cacheData;
    }

    /**
     * Returns the value of a dotted offset
     *
     * @param  string $key a dotted offset
     * @return mixed
     */
    protected function read($key)
    {
        $value = null;
        if (isset($this->source[$key])) {
            $value = $this->source[$key];
        } else {
            //            xxx($this->source);
        }

        return $this->createModelRelation($key, $value);
    }

    /**
     * Sets the value of a dotted offset
     * @param string $key a dotted offset
     * @param $value
     */

    protected function write($key, $value)
    {
        $this->source[$key] = $value;
    }
}
