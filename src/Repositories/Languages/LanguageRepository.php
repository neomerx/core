<?php namespace Neomerx\Core\Repositories\Languages;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Repositories\CodeBasedResourceRepository;

class LanguageRepository extends CodeBasedResourceRepository implements LanguageRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Language::BIND_NAME);
    }

    /**
     * @inheritdoc
     */
    public function instance(array $attributes)
    {
        /** @var Language $resource */
        $resource = $this->makeModel();
        $this->fill($resource, $attributes);
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function fill(Language $resource, array $attributes = null)
    {
        $this->fillModel($resource, [], $attributes);
    }
}
