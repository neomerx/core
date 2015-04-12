<?php namespace Neomerx\Core\Repositories;

/**
 * @package Neomerx\Core
 */
abstract class CodeBasedResourceRepository extends BaseRepository
{
    /**
     * @inheritdoc
     */
    public function read($code, array $scopes = [], array $columns = ['*'])
    {
        return $this->findModelByCode($code, $scopes, $columns);
    }
}
