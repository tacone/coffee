<?php

namespace Tacone\Coffee\DataSource\Relation;

use Illuminate\Database\Eloquent\Model;
use Tacone\Coffee\DataSource\DataSource;

class HasOneWrapper extends AbstractWrapper
{
    public function saveAfter(Model $child)
    {
        // this is what `$this->relation->save($child)` does
        // we inline it here to wrap the save() method

        $child->setAttribute($this->relation->getPlainForeignKey(), $this->relation->getParentKey());
        $result = DataSource::make($child)->save();

        return $result;
    }

    public function associate($key, Model $model)
    {
        $this->relation->getParent()->setRelation($key, $model);
    }

    /**
     * @return Model
     */
    public function getChild()
    {
        return $this->relation->getRelated();
    }
}
