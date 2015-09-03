<?php namespace Neomerx\Core\Repositories\Languages;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Repositories\BaseRepository;

/**
 * @package Neomerx\Core
 */
class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Language::class);
    }

    /**
     * @inheritdoc
     */
    public function create(array $attributes)
    {
        return $this->createWith($attributes, []);
    }

    /**
     * @inheritdoc
     */
    public function update(Language $resource, array $attributes)
    {
        $this->updateWith($resource, $this->filterNulls($attributes), []);
    }
}
