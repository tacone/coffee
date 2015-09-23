<?php

namespace Tacone\Coffee\DataSource\Relation;

use Illuminate\Database\Eloquent\Model;
use Tacone\Coffee\DataSource\DataSource;

class BelongsToWrapper extends AbstractWrapper
{
    /**
     * @param Model $child
     */
    public function saveBefore(Model $child)
    {
        $result = DataSource::make($child)->save();
        $this->relation->associate($child);

        return $result;
    }

    public function associate($key, Model $model)
    {
        $this->relation->associate($model);
    }

    /**
     * @return Model
     */
    public function getChild()
    {
        return $this->relation->getRelated();
    }
}
