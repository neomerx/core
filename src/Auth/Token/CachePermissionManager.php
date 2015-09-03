<?php namespace Neomerx\Core\Auth\Token;

use \Closure;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\ObjectType;
use \Neomerx\Core\Models\RoleObjectType;
use \Neomerx\Core\Auth\ObjectIdentityInterface;
use \Neomerx\Core\Exceptions\AccessDeniedException;
use \Illuminate\Contracts\Auth\Guard as AuthInterface;
use \Illuminate\Contracts\Cache\Repository as CacheInterface;
use \Illuminate\Contracts\Container\Container as ContainerInterface;
use \Neomerx\Core\Repositories\Auth\RoleObjectTypeRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 *
 * @package Neomerx\Core
 */
class CachePermissionManager implements RolePermissionManagerInterface
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var int
     */
    private $cacheDuration;

    /**
     * @param AuthInterface      $auth
     * @param CacheInterface     $cache
     * @param ContainerInterface $container
     * @param string             $cacheKey
     * @param int                $cacheDuration
     */
    public function __construct(
        AuthInterface $auth,
        CacheInterface $cache,
        ContainerInterface $container,
        $cacheKey,
        $cacheDuration = 5
    ) {
        assert('is_int($cacheDuration) && $cacheDuration > 0');

        $this->auth          = $auth;
        $this->cache         = $cache;
        $this->container     = $container;
        $this->cacheKey      = $cacheKey;
        $this->cacheDuration = $cacheDuration;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function has(ObjectIdentityInterface $object, Permission $permission)
    {
        $currentUser      = $this->auth->user();
        list($objectType) = $object->getIdentifier();

        $userPermissionMask = 0;

        if (isset($currentUser->{self::USER_AUTH_ROLES}) === true) {
            $rolesActions = $this->cache
                ->remember($this->cacheKey, $this->cacheDuration, $this->getRoleAccessListClosure());

            $allowedMask = 0;
            $deniedMask  = 0;
            foreach ($currentUser->{self::USER_AUTH_ROLES} as $roleId => $roleCode) {
                if (isset($rolesActions[$objectType][$roleId]) === true) {
                    list($roleAllowedMask, $roleDeniedMask) = $rolesActions[$objectType][$roleId];
                    $allowedMask |= (int)$roleAllowedMask;
                    $deniedMask  |= (int)$roleDeniedMask;
                }
            }
            $userPermissionMask = ($allowedMask & ~$deniedMask);
        }

        return Permission::canPass($permission, $userPermissionMask);
    }

    /**
     * @inheritdoc
     */
    public function check(ObjectIdentityInterface $object, Permission $permission)
    {
        $this->has($object, $permission) === true ?: S\throwEx(new AccessDeniedException($permission));
    }

    /**
     * @return Closure
     */
    private function getRoleAccessListClosure()
    {
        return function () {
            /** @var RoleObjectTypeRepositoryInterface $roleObjectTypeRepo */
            $roleObjectTypeRepo = $this->container->make(RoleObjectTypeRepositoryInterface::class);
            $allPermissions     = $roleObjectTypeRepo->index([RoleObjectType::withType()]);

            $result = [];
            foreach ($allPermissions as $permission) {
                /** @var RoleObjectType $permission */
                $roleId      = $permission->{RoleObjectType::FIELD_ID_ROLE};
                $allowedMask = $permission->{RoleObjectType::FIELD_ALLOW_MASK};
                $deniedMask  = $permission->{RoleObjectType::FIELD_DENY_MASK};
                $objectType  = $permission->{RoleObjectType::FIELD_TYPE}->{ObjectType::FIELD_TYPE};

                $result[$objectType][$roleId] = [$allowedMask, $deniedMask];
            }
            return $result;
        };
    }
}
