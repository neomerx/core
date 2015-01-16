<?php namespace Neomerx\Core\Models;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Exceptions\Exception;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Auth\ObjectIdentityInterface;
use \Neomerx\Core\Exceptions\ValidationException;

/**
 * Validation rules could differ for same model depending on the usage scenario.
 * For example, let's take Employee model. Input data should have 'password confirmation' field
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
 * @method static Builder with($conditions)
 * @method static Builder where($conditions)
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class BaseModel extends Model implements BaseModelInterface, ObjectIdentityInterface
{
    use RelationsTrait;
    use ValidationTrait;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->initRelationsTrait($this);
        $this->initValidationTrait($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModelMorphs($name, $type, $modelId)
    {
        return $this->getMorphs($name, $type, $modelId);
    }

    /**
     * {@inheritdoc}
     *
     * Add 'pre' and 'post' event handlers for each individual model.
     * They are used for handling validation and sync underlying model assets such as files.
     */
    protected function fireModelEvent($event, $halt = true)
    {
        $handler = S\arrayGetValue([
            'creating'  => 'onCreating',
            'created'   => 'onCreated',
            'updating'  => 'onUpdating',
            'updated'   => 'onUpdated',
            'deleting'  => 'onDeleting',
            'deleted'   => 'onDeleted',
            'saving'    => 'onSaving',
            'saved'     => 'onSaving',
            'restoring' => 'onRestoring',
            'restored'  => 'onRestored',
        ], $event);

        $modelEventResult = true;
        if ($handler !== null) {
            $modelEventResult = $this->{$handler}();
        }

        /** @noinspection PhpUndefinedClassInspection */
        $parentEventResult = parent::fireModelEvent($event, $halt);
        $result = ($modelEventResult === false or $parentEventResult === false) ? false : $parentEventResult;

        return $result;
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
     * @param $input
     *
     * @return BaseModel
     */
    public function createOrFailResource($input)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->validateInputOnCreate($input);
        $validator->passes() === true ?: S\throwEx(new ValidationException($validator));

        /** @var BaseModel $resource */
        $resource = $this->create($input);
        $resource->exists === true ?: S\throwEx(new ValidationException($resource->getValidator()));

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
        $this->save() === true ?: S\throwEx(new ValidationException($this->getValidator()));
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
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $this->validateInputOnUpdate($input);
        $validator->passes() === true ?: S\throwEx(new ValidationException($validator));
        $this->update($input) === true ?: S\throwEx(new ValidationException($this->getValidator()));
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
        ($this->delete() === true) ?: S\throwEx(new Exception());
        return true;
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
        $connection = $this->getConnection()->getName();
        foreach ($stdClasses as $stdClass) {
            $models[] = $model = $this->newFromBuilder($stdClass);
            $model->setConnection($connection);
        }

        // If 'Eager Loading' is used while selecting objects it will load them as well.
        // This snippet is based on Laravel source code from \Illuminate\Database\Eloquent\Builder::get
        if (empty($models) === false) {
            $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->newCollection($models);
    }
}
