<?php namespace Neomerx\Core\Auth;

class CreatePermission extends Permission
{
    public function __construct()
    {
        parent::__construct(self::CREATE);
    }
}
