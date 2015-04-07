<?php namespace Neomerx\Core\Exceptions;

use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Support\Translate as T;

class AccessDeniedException extends Exception
{
    /**
     * @var Permission
     */
    private $permission;

    /**
     * {@inheritDoc}
     */
    public function __construct(Permission $permission, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->loadIfEmpty($message, T::KEY_EX_ACCESS_DENIED_EXCEPTION), $code, $previous);
        $this->permission = $permission;
    }

    /**
     * @return Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
