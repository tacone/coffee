<?php

namespace Tacone\Coffee\DataSource\Relation;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tacone\Coffee\DataSource\DataSource;

class HasManyWrapper extends AbstractWrapper
{
    public function saveAfter(Collection $children)
    {
        $result = [];
        foreach ($children as $child) {
            $child->setAttribute($this->relation->getPlainForeignKey(), $this->relation->getParentKey());
            $result[] = DataSource::make($child)->save();
        }

        return $result;
    }

    public function associate($key, Model $model)
    {

        /** @var Collection $collection */
        $collection = $this->relation->getParent()->$key;
        foreach ($collection as $position => $m) {
            // already in there
            if ($model === $m) {
                return;
            }

            if ($model->getKey() && $model->getKey() == $m->getKey()) {
                // found a different instance with the same key, substitute it
                $collection[$position] = $model;

                return;
            }
        }
        // if it get's to here, there's no matching model, so we just append the
        // new one at the end of the collection
        $collection[] = $model;
    }

    /**
     * @return Model
     */
    public function getChild()
    {
        return $this->relation->getRelated()->newCollection();
    }
}
