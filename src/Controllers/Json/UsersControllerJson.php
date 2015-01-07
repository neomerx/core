<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Models\User;
use \Neomerx\Core\Api\Facades\Users;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Converters\UserConverterGeneric;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class UsersControllerJson extends BaseControllerJson
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        parent::__construct(Users::INTERFACE_BIND_NAME, App::make(UserConverterGeneric::BIND_NAME));
    }

    /**
     * Search users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function index()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('searchImpl', [$input]);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    protected function searchImpl(array $parameters)
    {
        $result = [];
        foreach ($this->getApiFacade()->search($parameters) as $user) {
            $result[] = $this->getConverter()->convert($user);
        }

        return [$result, null];
    }

    /**
     * @param array $input
     *
     * @return array
     */
    protected function createResource(array $input)
    {
        $user = $this->getApiFacade()->create($input);
        return [['id' => $user->{User::FIELD_ID}], SymfonyResponse::HTTP_CREATED];
    }
}
