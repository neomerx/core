<?php namespace Neomerx\Core\Auth;

class RestorePermission extends Permission
{
    public function __construct()
    {
        parent::__construct(self::RESTORE);
    }
}
