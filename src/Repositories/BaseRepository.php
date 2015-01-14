<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use \Neomerx\Core\Models\SelectByCodeInterface;

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
        assert('isset($modelBindName) and is_subclass_of(\''.$modelBindName.'\', \''.BaseModel::class.'\')');

        $this->modelBindName = $modelBindName;
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
     * @param string $code
     * @param array  $scopes
     *
     * @return Builder
     */
    protected function makeBuilderByCode($code, array $scopes = [])
    {
        assert('is_subclass_of(\''.$this->modelBindName.'\', \''.SelectByCodeInterface::class.'\')');

        /** @var SelectByCodeInterface $model */
        $model = $this->getUnderlyingModel();
        return $this->callMethodsOnBuilder($model->selectByCode($code), $scopes);
    }

    /**
     * @param int   $modelId
     * @param array $scopes
     *
     * @return Builder
     */
    protected function makeBuilderById($modelId, array $scopes = [])
    {
        $model = $this->getUnderlyingModel();
        $keyColumn = $model->getKeyName();
        return $this->callMethodsOnBuilder($model->newQuery()->where($keyColumn, $modelId), $scopes);
    }

    /**
     * @param Builder $builder
     * @param array   $scopes
     *
     * @return Builder
     */
    protected function callMethodsOnBuilder(Builder $builder, array $scopes)
    {
        foreach ($scopes as $scope) {
            $builder->$scope();
        }

        /** @var Builder $builder */
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
     * Search resources.
     * If both $parameters and $rules are not specified then all resources will be returned.
     *
     * @param array      $scopes
     * @param array|null $parameters
     * @param array|null $rules
     * @param array      $columns
     *
     * @return Collection
     */
    public function search(array $scopes = [], array $parameters = null, array $rules = null, array $columns = ['*'])
    {
        $builder = $this->callMethodsOnBuilder($this->getUnderlyingModel()->newQuery(), $scopes);

        if (empty($parameters) === false and empty($rules) === false) {
            $parser  = new SearchParser(new SearchGrammar($builder), $rules);
            $builder = $parser->buildQuery($parameters);
        }

        /** @var Collection $result */
        $result = $builder->get($columns);

        return $result;
    }
}
