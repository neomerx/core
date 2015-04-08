<?php namespace Neomerx\Core\Auth;

class ViewPermission extends Permission
{
    public function __construct()
    {
        parent::__construct(self::VIEW);
    }
}
