<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Support\Facades\App;

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
     * @param int $modelId
     *
     * @return BaseModel
     */
    protected function findModel($modelId)
    {
        /** @var \Neomerx\Core\Models\BaseModel $model */
        $model = $this->getUnderlyingModel()->newQuery()->findOrFail($modelId);
        return $model;
    }
}
