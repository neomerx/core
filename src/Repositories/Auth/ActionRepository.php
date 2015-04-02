<?php namespace Neomerx\Core\Repositories\Auth;

use \Neomerx\Core\Models\Action;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class ActionRepository extends CodeBasedResourceRepository implements ActionRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Action::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var Action $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Action $resource, array $attributes)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
