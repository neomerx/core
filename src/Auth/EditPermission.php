<?php namespace Neomerx\Core\Auth;

class EditPermission extends Permission
{
    public function __construct()
    {
        parent::__construct(self::EDIT);
    }
}
