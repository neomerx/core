<?php namespace Neomerx\Core\Models;

use \Illuminate\Database\Eloquent\Builder;

interface SelectByCodeInterface
{
    /**
     * @param string $code
     *
     * @return Builder
     */
    public function selectByCode($code);
}
