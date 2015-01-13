<?php namespace Neomerx\Core\Models;

use \Illuminate\Support\Facades\App;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\MorphTo;
use \Illuminate\Database\Eloquent\Relations\MorphOne;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\MorphMany;
use \Illuminate\Database\Eloquent\Relations\MorphToMany;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Database\Eloquent\Relations\HasManyThrough;

trait RelationsTrait
{
    /**
     * @var BaseModelInterface
     */
    private $baseModelRT;

    public function initRelationsTrait(BaseModelInterface $baseModel)
    {
        $this->baseModelRT = $baseModel;
    }

    /**
     * @param string $related
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relation
     *
     * @return BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        // avoid 'unused' warning. This parameter is needed for compatibility with Model::belongsTo
        $relation ?: null;

        /** @noinspection PhpUndefinedMethodInspection */
        return new BelongsTo(
            App::make($related)->newQuery(),
            $this->rtModel(),
            $foreignKey,
            $otherKey,
            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function']
        );
    }

    /**
     * @param string $related
     * @param string $table
     * @param string $foreignKey
     * @param string $otherKey
     * @param string $relation
     *
     * @return BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        // avoid 'unused' warning. This parameter is needed for compatibility with Model::belongsToMany
        $relation ?: null;

        /** @noinspection PhpUndefinedMethodInspection */
        return new BelongsToMany(
            App::make($related)->newQuery(),
            $this->rtModel(),
            $table,
            $foreignKey,
            $otherKey,
            $this->getCaller()
        );
    }

    /**
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BaseModel $instance */
        $instance = App::make($related);
        return new HasOne($instance->newQuery(), $this->rtModel(), $this->rtTable($instance, $foreignKey), $localKey);
    }

    /**
     * @param string $related
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BaseModel $instance */
        $instance = App::make($related);
        return new HasMany($instance->newQuery(), $this->rtModel(), $this->rtTable($instance, $foreignKey), $localKey);
    }

    /**
     * @param string $related
     * @param string $through
     * @param string $firstKey
     * @param string $secondKey
     *
     * @return HasManyThrough
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return new HasManyThrough(
            App::make($related)->newQuery(),
            $this->rtModel(),
            App::make($through),
            $firstKey,
            $secondKey
        );
    }

    /**
     * @param string $name
     * @param string $itemType
     * @param string $itemId
     *
     * @return MorphTo
     */
    public function morphTo($name = null, $itemType = null, $itemId = null)
    {
        list($itemType, $itemId) = $this->baseModelRT->getModelMorphs($name, $itemType, $itemId);

        if (is_null($class = $this->$itemType)) {
            return new MorphTo(
                $this->rtModel()->newQuery(),
                $this->rtModel(),
                $itemId,
                null,
                $itemType,
                $name
            );
        } else {
            /** @noinspection PhpUndefinedMethodInspection */
            /** @var BaseModel $instance */
            $instance = App::make($class);
            return new MorphTo(
                $instance->newQuery(),
                $this->rtModel(),
                $itemId,
                $instance->getKeyName(),
                $itemType,
                $name
            );
        }
    }

    /**
     * @param string $related
     * @param string $name
     * @param string $itemType
     * @param string $itemId
     * @param string $localKey
     *
     * @return MorphOne
     */
    public function morphOne($related, $name, $itemType = null, $itemId = null, $localKey = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BaseModel $instance */
        $instance = App::make($related);

        list($itemType, $itemId) = $this->baseModelRT->getModelMorphs($name, $itemType, $itemId);
        $table    = $instance->getTable();
        $localKey = $localKey ?: $this->baseModelRT->getModel()->getKeyName();

        return new MorphOne(
            $instance->newQuery(),
            $this->baseModelRT->getModel(),
            $table.'.'.$itemType,
            $table.'.'.$itemId,
            $localKey
        );
    }

    /**
     * @param string $related
     * @param string $name
     * @param string $itemType
     * @param string $itemId
     * @param string $localKey
     *
     * @return MorphMany
     */
    public function morphMany($related, $name, $itemType = null, $itemId = null, $localKey = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BaseModel $instance */
        $instance = App::make($related);

        list($itemType, $itemId) = $this->baseModelRT->getModelMorphs($name, $itemType, $itemId);
        $table    = $instance->getTable();
        $localKey = $localKey ?: $this->baseModelRT->getModel()->getKeyName();

        return new MorphMany(
            $instance->newQuery(),
            $this->baseModelRT->getModel(),
            $table.'.'.$itemType,
            $table.'.'.$itemId,
            $localKey
        );
    }


    /**
     * @param string $related
     * @param string $name
     * @param string $table
     * @param string $foreignKey
     * @param string $otherKey
     * @param bool   $inverse
     *
     * @return MorphToMany
     */
    public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var BaseModel $instance */
        $instance   = App::make($related);
        $caller     = $this->getCaller();
        $foreignKey = $foreignKey ?: $name.'_id';
        $otherKey   = $otherKey ?: $instance->getForeignKey();
        $query      = $instance->newQuery();
        $table      = $table ?: str_plural($name);

        return new MorphToMany(
            $query,
            $this->baseModelRT->getModel(),
            $name,
            $table,
            $foreignKey,
            $otherKey,
            $caller,
            $inverse
        );
    }

    /**
     * Get the relationship name of the belongs to many.
     *
     * @return  string
     */
    private function getCaller()
    {
        $self = __FUNCTION__;

        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $stackFrame) {
            $caller = $stackFrame['function'];
            if ($caller !== $self and !in_array($caller, Model::$manyMethods)) {
                return $caller;
            }
        }

        return null;
    }

    /**
     * @return Model
     */
    private function rtModel()
    {
        return $this->baseModelRT->getModel();
    }

    /**
     * @param BaseModel $instance
     * @param string    $foreignKey
     *
     * @return string
     */
    private function rtTable(BaseModel $instance, $foreignKey)
    {
        return $instance->getTable().'.'.$foreignKey;
    }
}
