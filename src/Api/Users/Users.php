<?php namespace Neomerx\Core\Api\Users;

use \Neomerx\Core\Models\User;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Events\Event;
use \Neomerx\Core\Auth\Permission;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Support\SearchParser;
use \Neomerx\Core\Support\SearchGrammar;
use \Neomerx\Core\Auth\Facades\Permissions;

class Users implements UsersInterface
{
    const BIND_NAME = __CLASS__;

    /**
     * @var User
     */
    private $user;

    /**
     * Searchable fields of the resource.
     * Could be used as parameters in search function.
     *
     * @var array
     */
    protected static $searchRules = [
        User::FIELD_FIRST_NAME    => SearchGrammar::TYPE_STRING,
        User::FIELD_LAST_NAME     => SearchGrammar::TYPE_STRING,
        User::FIELD_EMAIL         => SearchGrammar::TYPE_STRING,
        User::FIELD_ACTIVE        => SearchGrammar::TYPE_BOOL,
        SearchGrammar::LIMIT_SKIP => SearchGrammar::TYPE_LIMIT,
        SearchGrammar::LIMIT_TAKE => SearchGrammar::TYPE_LIMIT,
    ];

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $input)
    {
        $user = null;

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            $userRoles = S\array_get_value($input, self::PARAM_ROLES);
            unset($input[self::PARAM_ROLES]);

            /** @var \Neomerx\Core\Models\User $user */
            $user = $this->user->createOrFailResource($input);
            Permissions::check($user, Permission::create());

            if (!empty($userRoles)) {
                foreach ($userRoles as $role) {
                    $user->addRole($role);
                }
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        if ($user !== null) {
            Event::fire(new UserArgs(self::EVENT_PREFIX . 'created', $user));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function read($userId)
    {
        /** @var \Neomerx\Core\Models\User $user */
        /** @noinspection PhpUndefinedMethodInspection */
        $user = $this->user->selectById($userId)->withRoles()->firstOrFail();
        Permissions::check($user, Permission::view());
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function update($userId, array $input)
    {
        $userRoles = S\array_get_value($input, self::PARAM_ROLES);
        unset($input[self::PARAM_ROLES]);

        /** @var \Neomerx\Core\Models\User $user */
        $user = $this->user->findOrFail($userId);
        Permissions::check($user, Permission::edit());

        if ($userRoles !== null) {
            /** @noinspection PhpUndefinedMethodInspection */
            $curUserRoles  = $user->roles()->lists('code');
            $rolesToAdd    = array_diff($userRoles, $curUserRoles);
            $rolesToRemove = array_diff($curUserRoles, $userRoles);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        DB::beginTransaction();
        try {

            empty($input) ?: $user->updateOrFail($input);

            if (!empty($rolesToAdd)) {
                foreach ($rolesToAdd as $role) {
                    $user->addRole($role);
                }
            }

            if (!empty($rolesToRemove)) {
                foreach ($rolesToRemove as $role) {
                    $user->removeRole($role);
                }
            }

            $allExecutedOk = true;

        } finally {
            /** @noinspection PhpUndefinedMethodInspection */
            isset($allExecutedOk) ? DB::commit() : DB::rollBack();
        }

        Event::fire(new UserArgs(self::EVENT_PREFIX . 'updated', $user));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($userId)
    {
        /** @var \Neomerx\Core\Models\User $user */
        $user = $this->user->findOrFail($userId);

        Permissions::check($user, Permission::delete());

        $user->deleteOrFail();

        Event::fire(new UserArgs(self::EVENT_PREFIX . 'deleted', $user));
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $parameters = [])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $builder = $this->user->newQuery()->withRoles();

        // add search parameters if required
        if (!empty($parameters)) {
            $parser  = new SearchParser(new SearchGrammar($builder), static::$searchRules);
            $builder = $parser->buildQuery($parameters);
        }

        $users = $builder->get();

        foreach ($users as $user) {
            /** @var \Neomerx\Core\Models\User $user */
            Permissions::check($user, Permission::view());
        }

        return $users;
    }
}
