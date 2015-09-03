<?php namespace Neomerx\Core\Repositories;

use \DB;
use \Closure;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var BaseModel
     */
    private $model;

    /**
     * @var string
     */
    private $modelBindName;

    /**
     * @param string $modelBindName
     */
    public function __construct($modelBindName)
    {
        assert('is_subclass_of(\''.$modelBindName.'\', \''.BaseModel::class.'\')');

        $this->modelBindName = $modelBindName;
    }

    /**
     * @inheritdoc
     */
    public function index(array $relations = [], array $columns = ['*'])
    {
        $builder = $this->getUnderlyingModel()->newQuery();
        $result  = $builder->with($relations)->get($columns);

        return $result;
    }

    /**
     * Create resource with attributes and relationships.
     *
     * @param array $attributes
     * @param array $relationships
     *
     * @return BaseModel
     */
    protected function createWith(array $attributes, array $relationships)
    {
        $model = $this->createModel();
        $this->fillModel($model, $attributes, $relationships);
        $model->saveOrFail();

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function read($index, array $scopes = [], array $columns = ['*'])
    {
        $model = $this->getUnderlyingModel();
        $keyColumn = $model->getKeyName();
        $builder = $model->newQuery()->where($keyColumn, $index);
        empty($scopes) === true ?: $builder->with($scopes);

        return $builder->firstOrFail($columns);
    }

    /**
     * Create resource with attributes and relationships.
     *
     * @param BaseModel $resource
     * @param array     $attributes
     * @param array     $relationships
     *
     * @return void
     */
    protected function updateWith(BaseModel $resource, array $attributes, array $relationships)
    {
        $this->fillModel($resource, $attributes, $relationships);
        $resource->saveOrFail();
    }

    /**
     * @inheritdoc
     */
    public function delete($index)
    {
        $model   = $this->getUnderlyingModel();
        $query   = $model->newQuery()->where($model->getKeyName(), '=', $index);
        $deleted = $query->delete();

        return $deleted > 0;
    }

    /**
     * @return BaseModel
     */
    protected function getUnderlyingModel()
    {
        $this->model !== null ?: ($this->model = $this->createModel());
        return $this->model;
    }

    /**
     * @return BaseModel
     */
    protected function createModel()
    {
        return app($this->modelBindName);
    }

    /**
     * @param BaseModel  $model
     * @param array      $attributes
     * @param array      $relationships
     *
     * @return $this
     */
    protected function fillModel(BaseModel $model, array $attributes, array $relationships)
    {
        empty($attributes) === true ?: $model->fill($attributes);

        foreach ($relationships as $relationshipColumn => $relationshipId) {
            $model->setAttribute($relationshipColumn, $relationshipId);
        }

        return $this;
    }

    /**
     * @param BaseModel|null $model
     *
     * @return mixed
     */
    protected function idOf(BaseModel $model = null)
    {
        return $model === null ? null : $model->getKey();
    }

    /**
     * @param S\Nullable|null $value
     * @param string|null     $assertClass
     *
     * @return S\Nullable|null
     */
    protected function idOfNullable(S\Nullable $value = null, $assertClass = null)
    {
        // suppress 'unused' warning
        $assertClass ?: null;

        $result = null;

        if ($value !== null) {
            /** @var BaseModel|null $model */
            $model = $value->value;
            assert('$model === null || get_class($model) === $assertClass');
            $resourceId = $model === null ? null : $model->getKey();
            $result = new S\Nullable($resourceId, null);
        }

        return $result;
    }

    /**
     * @param BaseModel $model
     *
     * @return S\Nullable
     */
    protected function getNullable(BaseModel $model)
    {
        return new S\Nullable($model);
    }

    /**
     * @param Closure $closure
     *
     * @return void
     */
    protected function executeInTransaction(Closure $closure)
    {
        DB::beginTransaction();
        try {
            $closure();
            $allExecutedOk = true;
        } finally {
            isset($allExecutedOk) === true ? DB::commit() : DB::rollBack();
        }
    }

    /**
     * @param array $values
     * @param array $nullable
     *
     * @return array
     */
    protected function filterNulls(array $values, array $nullable = [])
    {
        $result = S\arrayFilterNulls($values);

        if (empty($nullable) === false) {
            $filteredNullable = S\arrayFilterNulls($nullable);
            if (empty($filteredNullable) === false) {
                $extractedNullable = array_map(function (S\Nullable $nullable) {
                    return $nullable->value;
                }, $filteredNullable);

                if (empty($extractedNullable) === false) {
                    $result = array_merge($result, $extractedNullable);
                }
            }
        }

        return $result;
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
        $model = $this->createModel();
        $connection = $model->getConnection();
        $connectionName = $connection->getName();
        foreach ($stdClasses as $stdClass) {
            $models[] = $model->newFromBuilder($stdClass, $connectionName);
        }

        // If 'Eager Loading' is used while selecting objects it will load them as well.
        // This snippet is based on Laravel source code from \Illuminate\Database\Eloquent\Builder::get
        if (empty($models) === false) {
            $queryBuilder =
                new QueryBuilder($connection, $connection->getQueryGrammar(), $connection->getPostProcessor());

            $models = $model->newEloquentBuilder($queryBuilder)->eagerLoadRelations($models);
        }

        return $model->newCollection($models);
    }
}
