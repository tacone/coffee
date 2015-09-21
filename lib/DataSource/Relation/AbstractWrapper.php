<?php

namespace Tacone\Coffee\DataSource\Relation;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class AbstractWrapper.
 *
 * @method saveBefore(Model $child)
 * @method saveAfter(Model $child)
 */
abstract class AbstractWrapper
{
    /**
     * @var Relation
     */
    protected $relation;

    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
    }

    public function canSaveBefore()
    {
        return method_exists($this, 'saveBefore');
    }

    public function canSaveAfter()
    {
        return method_exists($this, 'saveAfter');
    }

    abstract public function associate($key, Model $model);

    /**
     * @return Model|Collection
     */
    abstract public function getChild();
}
