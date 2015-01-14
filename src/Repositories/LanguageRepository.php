<?php namespace Neomerx\Core\Repositories;

use \Neomerx\Core\Models\Language;

interface LanguageRepository
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
}
