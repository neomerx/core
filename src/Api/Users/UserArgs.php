<?php namespace Neomerx\Core\Api\Users;

use \Neomerx\Core\Models\User;
use \Neomerx\Core\Events\EventArgs;

class UserArgs extends EventArgs
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param string    $name
     * @param User      $user
     * @param EventArgs $args
     */
    public function __construct($name, User $user, EventArgs $args = null)
    {
        parent::__construct($name, $args);
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        return $this->user;
    }
}
