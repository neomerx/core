<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Api\Facades\Roles;
use \Illuminate\Support\Facades\App;
use \Neomerx\Core\Converters\RoleConverterGeneric;

final class RolesControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Roles::INTERFACE_BIND_NAME, App::make(RoleConverterGeneric::BIND_NAME));
    }

    /**
     * Get all roles in the system.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        return $this->tryAndCatchWrapper('readAll', []);
    }

    /**
     * @return array
     */
    protected function readAll()
    {
        $result = [];
        foreach ($this->getApiFacade()->all() as $resource) {
            $result[] = $this->getConverter()->convert($resource);
        }

        return [$result, null];
    }
}
