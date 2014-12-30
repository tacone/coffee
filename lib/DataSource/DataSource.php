<?php


namespace Tacone\Coffee\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Tacone\Coffee\Base\DelegatedArrayTrait;

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
class DataSource implements \Countable, \IteratorAggregate, \ArrayAccess
{
    use DelegatedArrayTrait;

    protected $source;
    protected static $cache;

    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * @return \SplObjectStorage
     */
    public function cache()
    {
        if (!static::$cache) {
            static::$cache = new \SplObjectStorage();
        }

        return static::$cache;
    }

    /**
     * Get a new DataSource.
     * (factory method)
     *
     * @param $source
     * @return static
     */
    public static function make($source)
    {
        return new static($source);
    }

    /**
     * Get the wrapped model.
     * @return Model
     */
    public function unwrap()
    {
        return $this->getDelegatedStorage();
    }

    /**
     * Needed by DelegatedArrayTrait
     * @return mixed
     */
    protected function getDelegatedStorage()
    {
        return $this->source;
    }

    public function offsetGet($offset)
    {
        $key = null;
        $source = $this->find($offset, $key);
        if (is_object($source)) {
            return $source->read($key);
        }
        throw new \LogicException("Last source must be object");
    }

    /**
     * Given a dotted offset, returns the last token
     * corresponding model.
     *
     * (article.author.location.city will return the "location"
     * model)
     *
     * @param  string     $offset
     * @param $key pass an empty variable here.
     * @return DataSource
     */
    protected function find($offset, &$key)
    {
        $tokens = explode('.', $offset);
        $key = array_shift($tokens);

        if (!$tokens) {
            return $this;
        }
        $source = static::make($this->read($key));
        $offset = join('.', $tokens);

        return $source->find($offset, $key);
    }

    /**
     * Returns the cached relations for a given
     * parent model
     * @param $model
     * @return array
     */

    protected function relations(Model $model)
    {
        $cache = $this->cache();
        if (isset($cache[$model])) {
            return $cache[$model];
        }

        return [];
    }

    /**
     * Returns the value of a dotted offset
     *
     * @param  string $key a dotted offset
     * @return mixed
     */
    protected function read($key)
    {
        $value = $this->source->$key;
        if ($value instanceof DataSource) {
            $value = $value->unwrap();
        }

        return $this->createModelRelation($key, $value);
    }

    /**
     * This cache is made of strong and justice.
     *
     * We need to cache relations and models for each field we
     * read or change because Eloquent lazy loading actually
     * means that the developers have been lazy enough not
     * to give us a way to parse the relations of a model
     * before saving.
     *
     * Please note: this method writes a global (static) cache.
     *
     * @param string   $key      name of method on the main model
     *                           that returned the relation.
     * @param Relation $relation the relation object
     * @param Model    $model    the child model
     */
    protected function cacheRelation($key, Relation $relation, Model $model)
    {
        $cache = $this->cache();
        if (!isset($cache[$this->source])) {
            $cache[$this->source] = [];
        }
        $cacheData = $cache[$this->source];
        $cacheData[$key] = compact('model', 'relation');
        $cache[$this->source] = $cacheData;
    }

    /**
     * Caches a relation for later use.
     * Creates a new model in case of empty values.
     *
     * We need to invoke this method for every field
     * we want to save later, no exception
     *
     * @param $key
     * @param $model
     * @return mixed
     */
    protected function createModelRelation($key, $model)
    {
        if (!method_exists($this->source, $key)) {
            // not a relation
            return $model;
        }

        $relation = $this->source->$key();
        if (!$relation instanceof Relation) {
            // just a computed field
            return $model;
        }
        if ($model instanceof DataSource) {
            // just in case
            throw new \LogicException('Model should not be a datasource instance');
        }
        if (!$model instanceof Model) {
            // empty model, let's create one anew
            $model = $this->newModelFromRelation($key, $relation);
        }

        $relation = $this->source->$key();
        if (!$this->isSupportedRelation($relation)) {
            throw new \RuntimeException(
                "Unsupported relation " . get_class($relation)
                . "|" . get_class($model) . " found in " . get_class($this->source)
                . "::" . $key);
        }

        $this->cacheRelation($key, $relation, $model);

        return $model;
    }

    /**
     * Creates a new model instance for a relation or returns
     * an existing one.
     * Recycling already created models is needed, otherwise
     * multiple fields targetting the same related model will
     * overwrite each other with empty values.
     *
     * @param  string   $key
     * @param  Relation $relation
     * @return Model
     */
    protected function newModelFromRelation($key, Relation $relation)
    {
        // if you think those 2 issets can be simplified into just one
        // you've probably never dealt with \SplObjectStorage
        $cache = $this->cache();
        if (isset($cache[$this->source]) && isset($cache[$this->source][$key]['model'])) {
                if ($model = $cache[$this->source][$key]['model']) {
                    return $model;
            }
        }

        return $relation->getModel();
    }

    /**
     * Checks if the passed relation is supported
     * @param $relation
     * @return bool
     */
    protected function isSupportedRelation(Relation $relation)
    {
        if ($relation instanceof HasOne) {
            return true;
        }
        if ($relation instanceof BelongsTo) {
            return true;
        }

        return false;
    }

    /**
     * Sets the value of a dotted offset
     * @param string $key a dotted offset
     * @param $value
     */

    protected function write($key, $value)
    {
        $this->source->$key = $value;
    }

    public function offsetSet($offset, $value)
    {

        $key = null;
        $source = $this->find($offset, $key);
        if (is_object($source)) {
            $source->write($key, $value);

            return;
        }
        throw new \LogicException("Last source must be object");
    }

    public function offsetExists($offset)
    {
        return (boolean) $this->offsetGet($offset);
    }

    /**
     * Saves the wrapped model and all its loaded relations
     * in the right order.
     *
     * Only the relations that have been read or written through
     * this class are assured to be loaded. The remaining relations
     * will be ignored.
     */
    public function save()
    {
        $model = $this->unwrap();
        $modelRelations = $this->relations($model);

        // Cheat sheet
        //
        // - a female belongsTo a male
        // - each male hasOneOrMany females
        // - males have a $localKey, females don't and rely on a
        //   external foreignKey
        // - females need an pivot table to associate among them
        //
        // a model can be male and female at the same time
        //
        // we need to save females first, then the current model,
        // then each male

        foreach ($modelRelations as $key => $mr) {
            $daughter = $mr['model'];
            $relation = $mr['relation'];

            if ($relation instanceof BelongsTo) {
                $daughter->save();
                $relation->associate($daughter);
            }
        }
        $model->save();
        foreach ($modelRelations as $key => $model) {
            $son = $mr['model'];
            $relation = $mr['relation'];
            if ($relation instanceof HasOne) {
                $relation->save($son);
            }
        }
    }
}
