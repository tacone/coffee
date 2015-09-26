<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Tacone\Coffee\DataSource\EloquentCache as Cache;

class EloquentModelDataSource extends AbstractEloquentDataSource
{
    public function read($key)
    {
        return $this->getDelegatedStorage()->$key;
    }

    protected function write($key, $value)
    {
        // Models should be associated, not written
        if ($this->isEloquentObject($value)) {
            $this->cacheAndAssociate($key, $value);
//            var_dump(get_class(Cache::getRelation($this->getDelegatedStorage(), $key)));
            return;
        };

        $this->getDelegatedStorage()->$key = $value;
    }

    protected function makeAndBind($var, $key = null)
    {
        $source = DataSource::make($var);
        if ($var instanceof Collection && func_num_args() > 1) {
            $relation = Cache::getRelation($this->getDelegatedStorage(), $key);
            if (!$relation instanceof Relation) {
                throw new \LogicException('Expected Relation, got '.get_type_class($relation));
            }
            $source->bindToRelation($relation);
        }

        return $source;
    }

    protected function unsets($key)
    {
        if ($this->offsetExists($key)) {
            unset($this->getDelegatedStorage()->$key);
        }
    }
}
