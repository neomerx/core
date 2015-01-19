<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Language;

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
