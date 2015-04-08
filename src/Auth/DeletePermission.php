<?php namespace Neomerx\Core\Auth;

class DeletePermission extends Permission
{
    public function __construct()
    {
        parent::__construct(self::DELETE);
    }
}
