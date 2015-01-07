<?php namespace Neomerx\Core\Api\Users;

use \Neomerx\Core\Models\Role;
use \Neomerx\Core\Events\EventArgs;

class RoleArgs extends EventArgs
{
    /**
     * @var Role
     */
    private $role;

    /**
     * @param string    $name
     * @param Role      $role
     * @param EventArgs $args
     */
    public function __construct($name, Role $role, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->role = $role;
    }

    /**
     * @return Role
     */
    public function getModel()
    {
        return $this->role;
    }
}
