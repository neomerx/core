<?php namespace Neomerx\Core\Repositories\Languages;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Repositories\RepositoryInterface;
use \Neomerx\Core\Repositories\SearchableInterface;

interface LanguageRepositoryInterface extends RepositoryInterface, SearchableInterface
{
    /**
     * @param array $attributes
     *
     * @return Language
     */
    public function instance(array $attributes);

    /**
     * @param Language $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Language $resource, array $attributes = null);

    /**
     * @param string $code
     * @param array  $relations
     * @param array  $columns
     *
     * @return Language
     */
    public function read($code, array $relations = [], array $columns = ['*']);
}
