<?php

namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Tacone\Coffee\DataSource\Relation\AbstractWrapper;
use Tacone\Coffee\DataSource\Relation\BelongsToManyWrapper;
use Tacone\Coffee\DataSource\Relation\BelongsToWrapper;
use Tacone\Coffee\DataSource\Relation\HasManyWrapper;
use Tacone\Coffee\DataSource\Relation\HasOneWrapper;

class RelationApi
{
    /**
     * @var Collection
     */
    public static $relations;
//    const BEFORE = 'BEFORE';
//    const AFTER = 'AFTER';

    protected static function registerRelations()
    {
        if (!static::$relations) {
            $supported = [
                HasOne::class => HasOneWrapper::class,
                BelongsTo::class => BelongsToWrapper::class,
                BelongsToMany::class => BelongsToManyWrapper::class,
                HasMany::class => HasManyWrapper::class,
            ];

            static::$relations = Collection::make([]);

            foreach ($supported as $rel => $wrapperName) {
                static::$relations[$rel] = [
                    'className' => $wrapperName,
//                    static::BEFORE => method_exists($wrapperName, 'saveBefore'),
//                    static::AFTER => method_exists($wrapperName, 'saveAfter'),
                ];
            }
        }
    }

    /**
     * @param $relation
     *
     * @return AbstractWrapper
     */
    public static function make($relation)
    {
        if (static::isSupported($relation)) {
            $className = static::$relations[get_class($relation)]['className'];

            return new $className($relation);
        }
    }

    public static function isSupported($relation)
    {
        static::registerRelations();

        return isset(static::$relations[get_class($relation)]);
    }
}
