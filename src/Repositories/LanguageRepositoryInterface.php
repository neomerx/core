<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Language;

interface LanguageRepositoryInterface extends SearchableInterface
{
    /**
     * @param array|null $attributes
     *
     * @return Language
     */
    public function instance(array $attributes = null);

    /**
     * @param Language $resource
     * @param array|null $attributes
     *
     * @return void
     */
    public function fill(Language $resource, array $attributes = null);

    /**
     * @param string $code
     * @param array  $scopes
     * @param array  $columns
     *
     * @return Language
     */
    public function read($code, array $scopes = [], array $columns = ['*']);
}
