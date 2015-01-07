<?php namespace Neomerx\Core\Api;


interface CrudInterface
{
    /**
     * Create resource.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function create(array $input);

    /**
     * Read resource by identifier.
     *
     * @param string $code
     *
     * @return mixed
     */
    public function read($code);

    /**
     * Update resource.
     *
     * @param string $code
     * @param array  $input
     *
     * @return void
     */
    public function update($code, array $input);

    /**
     * Delete resource by identifier.
     *
     * @param string $code
     *
     * @return void
     */
    public function delete($code);
}
