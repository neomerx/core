<?php namespace Neomerx\Core\Auth\Token;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Contracts\Hashing\Hasher as HashInterface;
use \Illuminate\Contracts\Auth\Authenticatable as UserInterface;
use \Illuminate\Contracts\Auth\UserProvider as UserProviderInterface;

abstract class UserProvider implements UserProviderInterface
{
    /**
     * @var HashInterface
     */
    private $hashAlgorithm;

    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @param HashInterface         $hash
     * @param TokenManagerInterface $tokenManager
     * @param string                $modelClass
     */
    public function __construct(HashInterface $hash, TokenManagerInterface $tokenManager, $modelClass)
    {
        $this->hashAlgorithm = $hash;
        $this->modelClass    = $modelClass;
        $this->tokenManager  = $tokenManager;
    }

    /**
     * @inheritdoc
     */
    public function retrieveById($identifier)
    {
        $userModel = $this->getUserModel($identifier);

        return $userModel === null ? null : $this->modelToUser($userModel);
    }

    /**
     * @inheritdoc
     */
    public function retrieveByToken($identifier, $token)
    {
        $user      = null;
        $userModel = $this->getUserModel($identifier);
        $notFound  = ($userModel === null || ($user = $this->modelToUser($userModel)) === null ||
            $user->getRememberToken() !== $token);

        return $notFound === true ? null : $user;
    }

    /**
     * @inheritdoc
     */
    public function updateRememberToken(UserInterface $user, $token)
    {
        assert('$user instanceof ' . User::class);

        // Update token in the database

        $userModel = $this->getUserModel($user->getAuthIdentifier());

        assert('$userModel instanceof ' . UserInterface::class);
        /** @var UserInterface $userModel */

        $userModel->setRememberToken($token);

        /** @var Model $userModel */

        $userModel->save();

        // Update token in token manager

        $this->tokenManager->revokeToken($user->getRememberToken());
        if ($token !== null) {
            $this->tokenManager->saveToken($token, $this->userToTokenManagerPayload($this->modelToUser($userModel)));
        }

        $user->setRememberToken($token);
    }

    /**
     * @inheritdoc
     */
    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->createModelInstance()->newQuery();
        foreach ($credentials as $key => $value) {
            if (empty($key) === false && stripos($key, 'password') === false) {
                $query->where($key, '=', $value);
            }
        }

        $userModel = $query->with($this->getModelRelations())->first();

        return $userModel === null ? null : $this->modelToUser($userModel);
    }

    /**
     * @inheritdoc
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        assert('$user instanceof '.User::class);
        return isset($credentials['password']) && $this->hashAlgorithm->check(
            $credentials['password'],
            $user->getAuthPassword()
        );
    }

    /**
     * @return Model
     */
    protected function createModelInstance()
    {
        $instance = new $this->modelClass;
        assert('$instance instanceof '.Model::class);
        return $instance;
    }

    /**
     * @param int|string $identifier
     *
     * @return Model|null
     */
    protected function getUserModel($identifier)
    {
        $instance  = $this->createModelInstance();
        $relations = $this->getModelRelations();

        /** @var Model|null $model */
        $model = $instance->newQuery()->where($instance->getKeyName(), '=', $identifier)->with($relations)->first();

        assert('$model === null || $model instanceof '.Model::class);

        return $model;
    }

    /**
     * @param Model $userModel
     *
     * @return User
     */
    protected function modelToUser(Model $userModel)
    {
        $userAttributes = $userModel->attributesToArray();

        assert('$userModel instanceof '.UserInterface::class);

        /** @var UserInterface $userModel */

        return new User(
            $userModel->getAuthIdentifier(),
            $userModel->getAuthPassword(),
            $userModel->getRememberToken(),
            (object)$userAttributes
        );
    }

    /**
     * @param User $user
     *
     * @return string
     */
    protected function userToTokenManagerPayload(User $user)
    {
        return json_encode($user);
    }

    /**
     * @return array
     */
    abstract protected function getModelRelations();
}
