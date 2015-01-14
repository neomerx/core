<?php namespace Neomerx\Core\Repositories;

class IndexBasedResourceRepository extends BaseRepository
{
    /**
     * @inheritdoc
     */
    public function read($index, array $scopes = [], array $columns = ['*'])
    {
        return $this->findModelById($index, $scopes, $columns);
    }
}
