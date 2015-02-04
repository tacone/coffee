<?php


namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class DataSourceCollection extends DataSource
{
    public function __construct($source)
    {
        switch (true) {
            case $source instanceof \Eloquent:
                $this->source = $source;
                break;
            case $source instanceof Builder:
                $this->source = $source;
                break;
            case $source instanceof Collection:
                $this->source = $source;
                break;
            default:
                $type = is_object($source) ? get_class($source) : gettype($source);
                throw new \RuntimeException("Source of type $type is not supported");
        }
        parent::__construct($source);
    }
//
//    public function getPrimaryKey(){
//        switch (true) {
//            case $current instanceof \Eloquent:
//                return $current->getKey();
//                break;
//            case $current instanceof Builder:
//                return $current->getIdAttribute()->getKey();
//                break;
//        }
//        throw new \LogicException("Unexpected source");
//    }
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
