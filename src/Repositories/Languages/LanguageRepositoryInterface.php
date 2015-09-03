<?php namespace Neomerx\Core\Repositories\Languages;

use \Neomerx\Core\Models\Language;
use \Neomerx\Core\Repositories\RepositoryInterface;

/**
 * @package Neomerx\Core
 */
interface LanguageRepositoryInterface extends RepositoryInterface
{
    /**
     * @param array $attributes
     *
     * @return Language
     */
    public function create(array $attributes);

    /**
     * @param Language $resource
     * @param array    $attributes
     *
     * @return void
     */
    public function update(Language $resource, array $attributes);

    /**
     * @param int   $index
     * @param array $relations
     * @param array $columns
     *
     * @return Language
     */
    public function read($index, array $relations = [], array $columns = ['*']);
}
