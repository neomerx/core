<?php namespace Neomerx\Core\Models;

use \Validator;
use \Neomerx\Core\Support as S;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Exceptions\Exception;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Auth\ObjectIdentityInterface;
use \Neomerx\Core\Exceptions\ValidationException;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\MorphTo;
use \Illuminate\Database\Eloquent\Relations\MorphOne;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\MorphMany;
use \Illuminate\Database\Eloquent\Relations\MorphToMany;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Validation rules could differ for same model depending on the usage scenario.
 * For example, let's take User model. Input data should have 'password confirmation' field
 * however this field should be omitted and not checked on actual saving to database as
 * model does not have such field. Moreover input data could have rule limiting minimum password
 * length but when it hashed before saving to database such rule does not have any sense.
 * Also rules on update could also be different from 'on create' ones. For example, user email
 * should be unique and checked. However such check on update will fail as we updating existing
 * user.
 * So how many rules do I need? Our current understanding is up to four (1, 2 or 4).
 * If you don't have different data sets for inputs and actual save to database - you need 1 otherwise
 * multiply by 2 (e.g. hashing passwords, additional fields that should be checked but not saved).
 * If you don't need different checks on create and save (e.g. check unique) multiply by 1 otherwise by 2.
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class BaseModel extends Model implements BaseModelInterface, ObjectIdentityInterface
{
    /**
     * @var \Illuminate\Validation\Validator Stores validation result.
     */
    private $validator;

    /**
     * {@inheritdoc}
     *
     * We want having 'pre' and 'post' event handlers for each individual model.
     * They are used for handling validation and sync underlying model assets such as files.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function fireModelEvent($event, $halt = true)
    {
        $modelEventResult = true;
        switch ($event)
        {
            case 'creating':
                $modelEventResult = $this->onCreating();
                break;
            case 'created':
                $modelEventResult = $this->onCreated();
                break;
            case 'updating':
                $modelEventResult = $this->onUpdating();
                break;
            case 'updated':
                $modelEventResult = $this->onUpdated();
                break;
            case 'deleting':
                $modelEventResult = $this->onDeleting();
                break;
            case 'deleted':
                $modelEventResult = $this->onDeleted();
                break;
            case 'saving':
                $modelEventResult = $this->onSaving();
                break;
            case 'saved':
                $modelEventResult = $this->onSaved();
                break;
            case 'restoring':
                $modelEventResult = $this->onRestoring();
                break;
            case 'restored':
                $modelEventResult = $this->onRestored();
                break;
        }

        $parentEventResult = parent::fireModelEvent($event, $halt);
        $result = ($modelEventResult === false or $parentEventResult === false) ? false : $parentEventResult;

        return $result;
    }

    /**
     * Return validation result.
     *
     * @return \Illuminate\Support\Facades\Validator Validation result.
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Validate data against rules.
     *
     * @return bool Result.
     */
    public function isDataOnCreateValid()
    {
        return $this->validateAndStoreValidator($this->attributes, static::getDataOnCreateRules());
    }

    /**
     * Validate input against rules.
     *
     * @param $input array Input.
     *
     * @return bool Result.
     */
    public static function isInputOnCreateValid(array $input)
    {
        return Validator::make($input, static::getInputOnCreateRules())->passes();
    }

    /**
     * Validate data against rules.
     *
     * @return bool Result.
     */
    public function isDataOnUpdateValid()
    {
        return $this->validateAndStoreValidator($this->getDirty(), static::getDataOnUpdateRules());
    }

    /**
     * Called before model created.
     *
     * @return bool
     */
    protected function onCreating()
    {
        return $this->isDataOnCreateValid();
    }

    /**
     * Called after model created.
     *
     * @return bool
     */
    protected function onCreated()
    {
        return true;
    }

    /**
     * Called before model updated.
     *
     * @return bool
     */
    protected function onUpdating()
    {
        return $this->isDataOnUpdateValid();
    }

    /**
     * Called after model updated.
     *
     * @return bool
     */
    protected function onUpdated()
    {
        return true;
    }

    /**
     * Called before model deleted.
     *
     * @return bool
     */
    protected function onDeleting()
    {
        return true;
    }

    /**
     * Called after model deleted.
     *
     * @return bool
     */
    protected function onDeleted()
    {
        return true;
    }

    /**
     * Called before model saved.
     *
     * @return bool
     */
    protected function onSaving()
    {
        return true;
    }

    /**
     * Called after model saved.
     *
     * @return bool
     */
    protected function onSaved()
    {
        return true;
    }

    /**
     * Called before model restored.
     *
     * @return bool
     */
    protected function onRestoring()
    {
        return true;
    }

    /**
     * Called after model restored.
     *
     * @return bool
     */
    protected function onRestored()
    {
        return true;
    }

    /**
     * Validate input against rules.
     *
     * @param array $input Input.
     *
     * @return bool Result.
     */
    public static function isInputOnUpdateValid(array $input)
    {
        return Validator::make($input, static::getInputOnUpdateRules())->passes();
    }

    /**
     * Validates input on create.
     *
     * @param array $input
     *
     * @return \Illuminate\Validation\Validator
     */
    public static function validateInputOnCreate(array $input)
    {
        return Validator::make($input, static::getInputOnCreateRules());
    }

    /**
     * Validates input on update.
     *
     * @param array $input
     *
     * @return \Illuminate\Validation\Validator
     */
    public static function validateInputOnUpdate(array $input)
    {
        return Validator::make($input, static::getInputOnUpdateRules());
    }

    /**
     * Validates data against rules.
     *
     * @param array $data  Data.
     * @param array $rules Rules.
     *
     * @return bool Result.
     */
    private function validateAndStoreValidator(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $this->validator = $validator;
            return false;
        }

        return true;
    }
    /**
     * @param $input
     *
     * @return BaseModelInterface
     */
    public function createOrFailResource($input)
    {
        $validator = self::validateInputOnCreate($input);
        $validator->fails() ? S\throwEx(new ValidationException($validator)) : null;

        /** @var BaseModel $resource */
        $resource = $this->create($input);
        $resource->exists ? : S\throwEx(new ValidationException($resource->getValidator()));

        return $resource;
    }

    /**
     * @param $input
     *
     * @return mixed
     */
    public function createOrFail($input)
    {
        return $this->createOrFailResource($input)->{$this->getKeyName()};
    }

    /**
     * Save resource and check for validation errors.
     */
    public function saveOrFail()
    {
        $this->save() ?: S\throwEx(new ValidationException($this->getValidator()));
    }

    /**
     * @param $input
     *
     * @return void
     *
     * @throws \Neomerx\Core\Exceptions\ValidationException
     */
    public function updateOrFail($input)
    {
        $validator = self::validateInputOnUpdate($input);
        $validator->fails() ? S\throwEx(new ValidationException($validator)) : null;

        $this->update($input) ?: S\throwEx(new ValidationException($this->getValidator()));
    }

    /**
     * Delete model or throw an Exception.
     *
     * @return bool
     *
     * @throws \Neomerx\Core\Exceptions\Exception
     */
    public function deleteOrFail()
    {
        $deleted = $this->delete();
        $deleted ?:  S\throwEx(new Exception());
        return $deleted;
    }

    /**
     * @inheritdoc
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return new BelongsTo(
            App::make($related)->newQuery(),
            $this,
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
            $this,
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
        return new HasOne($instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $localKey);
    }

    /**
     * @inheritdoc
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        /** @var BaseModel $instance */
        /** @noinspection PhpUndefinedMethodInspection */
        $instance = App::make($related);
        return new HasMany($instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $localKey);
    }

    /**
     * @inheritdoc
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return new HasManyThrough(App::make($related)->newQuery(), $this, App::make($through), $firstKey, $secondKey);
    }

    /**
     * @inheritdoc
     */
    public function morphTo($name = null, $itemType = null, $itemId = null)
    {
        list($itemType, $itemId) = $this->getMorphs($name, $itemType, $itemId);

        if (is_null($class = $this->$itemType)) {
            return new MorphTo($this->newQuery(), $this, $itemId, null, $itemType, $name);
        } else {
            /** @var BaseModel $instance */
            /** @noinspection PhpUndefinedMethodInspection */
            $instance = App::make($class);
            return new MorphTo($instance->newQuery(), $this, $itemId, $instance->getKeyName(), $itemType, $name);
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

        list($itemType, $itemId) = $this->getMorphs($name, $itemType, $itemId);
        $table    = $instance->getTable();
        $localKey = $localKey ?: $this->getKeyName();

        return new MorphOne($instance->newQuery(), $this, $table.'.'.$itemType, $table.'.'.$itemId, $localKey);
    }

    /**
     * @inheritdoc
     */
    public function morphMany($related, $name, $itemType = null, $itemId = null, $localKey = null)
    {
        /** @var BaseModel $instance */
        /** @noinspection PhpUndefinedMethodInspection */
        $instance = App::make($related);

        list($itemType, $itemId) = $this->getMorphs($name, $itemType, $itemId);
        $table    = $instance->getTable();
        $localKey = $localKey ?: $this->getKeyName();

        return new MorphMany($instance->newQuery(), $this, $table.'.'.$itemType, $table.'.'.$itemId, $localKey);
    }

    /**
     * @inheritdoc
     */
    public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false)
    {
        /** @var BaseModel $instance */
        /** @noinspection PhpUndefinedMethodInspection */
        $instance   = App::make($related);
        $caller     = $this->getBelongsToManyCaller();
        $foreignKey = $foreignKey ?: $name.'_id';
        $otherKey   = $otherKey ?: $instance->getForeignKey();
        $query      = $instance->newQuery();
        $table      = $table ?: str_plural($name);

        return new MorphToMany($query, $this, $name, $table, $foreignKey, $otherKey, $caller, $inverse);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        $namespaceAndClass = explode('\\', get_class($this));
        return end($namespaceAndClass);
    }

    /**
     * Convert array of SdtClass objects to Collection of models.
     *
     * @param array $stdClasses
     *
     * @return Collection
     */
    protected function convertStdClassesToModels(array $stdClasses)
    {
        $models = [];
        $connection = $this->getConnection();
        foreach ($stdClasses as $stdClass) {
            $models[] = $model = $this->newFromBuilder($stdClass);
            $model->setConnection($connection);
        }

        // If 'Eager Loading' is used while selecting objects it will load them as well.
        // This snippet is based on Laravel source code from \Illuminate\Database\Eloquent\Builder::get
        if (!empty($models)) {
            $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->newCollection($models);
    }
}
