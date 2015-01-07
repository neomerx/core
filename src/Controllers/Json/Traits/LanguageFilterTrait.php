<?php namespace Neomerx\Core\Controllers\Json\Traits;

use \Neomerx\Core\Support as S;

trait LanguageFilterTrait
{
    /**
     * @param array $parameters
     *
     * @return mixed
     */
    private function getLanguageFilter(array $parameters)
    {
        return S\array_get_value($parameters, 'language');
    }
}
