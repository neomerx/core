<?php namespace Neomerx\Core\Repositories;

use \DB;
use \Closure;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Models\SelectByCodeInterface;

/**
 * @package Neomerx\Core
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
abstract class BaseRepository
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
        assert('isset($modelBindName) && is_subclass_of(\''.$modelBindName.'\', \''.BaseModel::class.'\')');
        assert('is_subclass_of(\''.get_class($this).'\', \''.RepositoryInterface::class.'\')');

        $this->modelBindName = $modelBindName;
    }
    /**
     * @inheritdoc
     */
    public function read($index, array $scopes = [], array $columns = ['*'])
    {
        return $this->findModelById($index, $scopes, $columns);
    }

    /**
     * @return BaseModel
     */
    protected function getUnderlyingModel()
    {
        $this->model !== null ?: ($this->model = $this->makeModel());
        return $this->model;
    }

    /**
     * @return BaseModel
     */
    protected function makeModel()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return App::make($this->modelBindName);
    }

    /**
     * @param array $relations
     *
     * @return Builder
     */
    protected function createBuilder(array $relations = [])
    {
        $builder = $this->getUnderlyingModel()->newQuery();
        empty($relations) === true ?: $builder->with($relations);

        return $builder;
    }

    /**
     * @param string $code
     * @param array  $relations
     *
     * @return Builder
     */
    protected function makeBuilderByCode($code, array $relations = [])
    {
        assert('is_subclass_of(\''.$this->modelBindName.'\', \''.SelectByCodeInterface::class.'\')');

        /** @var SelectByCodeInterface $model */
        $model = $this->getUnderlyingModel();
        $builder = $model->selectByCode($code);
        empty($relations) === true ?: $builder->with($relations);
        return $builder;
    }

    /**
     * @param int   $modelId
     * @param array $relations
     *
     * @return Builder
     */
    protected function makeBuilderById($modelId, array $relations = [])
    {
        $model = $this->getUnderlyingModel();
        $keyColumn = $model->getKeyName();
        $builder = $model->newQuery()->where($keyColumn, $modelId);
        empty($relations) === true ?: $builder->with($relations);
        return $builder;
    }

    /**
     * @param BaseModel  $model
     * @param array      $objects
     * @param array|null $attributes
     *
     * @return $this
     */
    protected function fillModel(BaseModel $model, array $objects, array $attributes = null)
    {
        empty($attributes) === true ?: $model->fill($attributes);
        foreach ($objects as $attribute => $srcModel) {
            /** @var BaseModel $srcModel */
            $srcModel === null ?: $model->setAttribute($attribute, $srcModel->getKey());
        }
        return $this;
    }

    /**
     * @param int   $modelId
     * @param array $scopes
     * @param array $columns
     *
     * @return BaseModel
     */
    protected function findModelById($modelId, array $scopes = [], array $columns = ['*'])
    {
        /** @var BaseModel $result */
        $result = $this->makeBuilderById($modelId, $scopes)->firstOrFail($columns);
        return $result;
    }

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return BaseModel
     */
    protected function findModelByCode($code, array $scopes = [], array $columns = ['*'])
    {
        /** @var BaseModel $result */
        $result = $this->makeBuilderByCode($code, $scopes)->firstOrFail($columns);
        return $result;
    }

    /**
     * @param Builder $builder
     * @param array   $columns
     *
     * @return Collection
     */
    protected function executeGet(Builder $builder, array $columns = ['*'])
    {
        /** @var Collection $result */
        $result = $builder->get($columns);

        return $result;
    }

    /**
     * @param Builder $builder
     * @param array   $columns
     *
     * @return BaseModel
     */
    protected function executeFirstOrFail(Builder $builder, array $columns = ['*'])
    {
        /** @var BaseModel $result */
        $result = $builder->firstOrFail($columns);

        return $result;
    }

    /**
     * Search resources.
     * If both $parameters and $rules are not specified then all resources will be returned.
     *
     * @param array      $relations
     * @param array|null $parameters
     * @param array|null $rules
     * @param array      $columns
     *
     * @return Collection
     */
    public function search(array $relations = [], array $parameters = null, array $rules = null, array $columns = ['*'])
    {
        $builder = $this->createBuilder($relations);

        if (empty($parameters) === false && empty($rules) === false) {
            $parser  = new SearchParser(new SearchGrammar($builder), $rules);
            $builder = $parser->buildQuery($parameters);
        }

        return $this->executeGet($builder, $columns);
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
}
