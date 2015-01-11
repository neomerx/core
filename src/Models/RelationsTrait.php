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
     * @inheritdoc
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return new BelongsTo(
            App::make($related)->newQuery(),
            $this->baseModelRT->getModel(),
            $foreignKey,
            $otherKey,
            debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function']
        );
    }

    /**
     * @inheritdoc
     */
    public function belongsToMany($related, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return new BelongsToMany(
            App::make($related)->newQuery(),
            $this->baseModelRT->getModel(),
            $table,
            $foreignKey,
            $otherKey,
            $this->getBelongsToManyCaller()
        );
    }

    /**
     * @inheritdoc
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        /** @var BaseModel $instance */
        /** @noinspection PhpUndefinedMethodInspection */
        $instance = App::make($related);
        return new HasOne(
            $instance->newQuery(),
            $this->baseModelRT->getModel(),
            $instance->getTable() . '.' . $foreignKey,
            $localKey
        );
    }

    /**
     * @inheritdoc
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        /** @var BaseModel $instance */
        /** @noinspection PhpUndefinedMethodInspection */
        $instance = App::make($related);
        return new HasMany(
            $instance->newQuery(),
            $this->baseModelRT->getModel(),
            $instance->getTable() . '.' . $foreignKey,
            $localKey
        );
    }

    /**
     * @inheritdoc
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return new HasManyThrough(
            App::make($related)->newQuery(),
            $this->baseModelRT->getModel(),
            App::make($through),
            $firstKey,
            $secondKey
        );
    }

    /**
     * @inheritdoc
     */
    public function morphTo($name = null, $itemType = null, $itemId = null)
    {
        list($itemType, $itemId) = $this->baseModelRT->getModelMorphs($name, $itemType, $itemId);

        if (is_null($class = $this->$itemType)) {
            return new MorphTo(
                $this->baseModelRT->getModel()->newQuery(),
                $this->baseModelRT->getModel(),
                $itemId,
                null,
                $itemType,
                $name
            );
        } else {
            /** @var BaseModel $instance */
            /** @noinspection PhpUndefinedMethodInspection */
            $instance = App::make($class);
            return new MorphTo(
                $instance->newQuery(),
                $this->baseModelRT->getModel(),
                $itemId,
                $instance->getKeyName(),
                $itemType,
                $name
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function morphOne($related, $name, $itemType = null, $itemId = null, $localKey = null)
    {
        /** @var BaseModel $instance */
        /** @noinspection PhpUndefinedMethodInspection */
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
     * @inheritdoc
     */
    public function morphMany($related, $name, $itemType = null, $itemId = null, $localKey = null)
    {
        /** @var BaseModel $instance */
        /** @noinspection PhpUndefinedMethodInspection */
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
     * @inheritdoc
     */
    public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false)
    {
        /** @var BaseModel $instance */
        /** @noinspection PhpUndefinedMethodInspection */
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
}
