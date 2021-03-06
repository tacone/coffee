<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class OldDataSourceCollection extends OldDataSource
{
    protected $parentRelation;

    public function __construct($source)
    {
        dd('dssd');
        switch (true) {
            case $source instanceof \Eloquent:
            case $source instanceof EloquentBuilder:
            case $source instanceof QueryBuilder:
            case $source instanceof Collection:
                $this->source = $source;
                break;
            default:
                $type = is_object($source) ? get_class($source) : gettype($source);
                throw new \RuntimeException("Source of type $type is not supported");
        }
        parent::__construct($source);
    }

    public function toArray()
    {
        return to_array($this->getDelegatedStorage());
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

//    protected function cacheRelation($key, Relation $relation, $model)
//    {
//        if (!$model instanceof Collection) {
//            throw new \LogicException("I can only cache Eloquent Collections, instance of " . get_class($model) . " given.");
//        }
//        $cache = $this->cache();
//        if (!isset($cache[$this->source])) {
//            $cache[$this->source] = [];
//        }
//        $cacheData = $cache[$this->source];
//        $cacheData[$key] = compact('model', 'relation');
//        $cache[$this->source] = $cacheData;
//    }

    /**
     * Returns the value of a dotted offset.
     *
     * @param string $key a dotted offset
     *
     * @return mixed
     */
    protected function read($key)
    {
        $value = null;
        if (isset($this->source[$key])) {
            $value = $this->source[$key];
        } else {
            echo "key $key is empty :( ";
        }

        return $this->createModelRelation($key, $value);
    }

    protected function relationMethodExists($key)
    {
        return true;
    }

    protected function getRelationForKey($key)
    {
        return $this->getParentRelation();
    }

    /**
     * Sets the value of a dotted offset.
     *
     * @param string $key a dotted offset
     * @param $value
     */
    protected function write($key, $value)
    {
        $this->log(get_class($this)."::write($key, ".get_type_class($value).')');
        $this->source[$key] = $value;
    }
}
