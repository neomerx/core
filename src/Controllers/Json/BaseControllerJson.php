<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Support as S;
use \Illuminate\Http\JsonResponse;
use \Neomerx\Core\Api\CrudInterface;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Illuminate\Support\Facades\Response;
use \Neomerx\Core\Controllers\BaseController;
use \Neomerx\Core\Converters\ConverterInterface;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Implements base class for CRUD operations by JSON protocol.
 * It's intended to be inherited and extended with more methods if needed.
 */
abstract class BaseControllerJson extends BaseController
{
    /**
     * @var mixed
     */
    private $apiFacade;

    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * @see CrudInterface
     * @see ConverterInterface
     *
     * @param string             $apiName   Api bind name.
     * @param ConverterInterface $converter
     */
    public function __construct($apiName, ConverterInterface $converter)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->apiFacade = App::make($apiName);
        $this->apiFacade instanceof CrudInterface ?: S\throwEx(new InvalidArgumentException('apiBindName'));

        /** @noinspection PhpUndefinedMethodInspection */
        $this->converter = $converter;
        $this->converter instanceof ConverterInterface ?: S\throwEx(new InvalidArgumentException('converter'));
    }

    /**
     * @return mixed
     */
    protected function getApiFacade()
    {
        return $this->apiFacade;
    }

    /**
     * @return ConverterInterface
     */
    protected function getConverter()
    {
        return $this->converter;
    }

    /**
     * Create a newly created resource in storage.
     * Default implementation.
     *
     * @return JsonResponse
     */
    final public function store()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('createResource', [$input]);
    }

    /**
     * Read the specified resource.
     * Default implementation.
     *
     * @param string $resourceCode
     *
     * @return JsonResponse
     */
    final public function show($resourceCode)
    {
        settype($resourceCode, 'string');
        return $this->tryAndCatchWrapper('readResource', [$resourceCode]);
    }

    /**
     * Update the specified resource in storage.
     * Default implementation.
     *
     * @param string $resourceCode
     *
     * @return JsonResponse
     */
    final public function update($resourceCode)
    {
        settype($resourceCode, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $input = Input::all();
        return $this->tryAndCatchWrapper('updateResource', [$resourceCode, $input]);
    }

    /**
     * Delete the specified resource from storage.
     * Default implementation.
     *
     * @param string $resourceCode
     *
     * @return JsonResponse
     */
    final public function destroy($resourceCode)
    {
        settype($resourceCode, 'string');
        /** @noinspection PhpUndefinedMethodInspection */
        $parameters = Input::all();
        return $this->tryAndCatchWrapper('deleteResource', [$resourceCode, $parameters]);
    }

    /**
     * @param array $input
     *
     * @return array<mixed,mixed>
     */
    protected function createResource(array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->apiFacade->create($input);
        return [null, SymfonyResponse::HTTP_CREATED];
    }

    /**
     * @param string $resourceCode
     *
     * @return array
     */
    protected function readResource($resourceCode)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $resource = $this->apiFacade->read($resourceCode);
        return [$this->converter !== null ? $this->converter->convert($resource) : $resource, null];
    }

    /**
     * @param string $resourceCode
     * @param array  $input
     *
     * @return array
     */
    protected function updateResource($resourceCode, array $input)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->apiFacade->update($resourceCode, $input);
        return $this->readResource($resourceCode);
    }

    /**
     * @param string $resourceCode
     * @param array  $parameters
     *
     * @return array
     */
    protected function deleteResource($resourceCode, array $parameters)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->apiFacade->delete($resourceCode, $parameters);
        return [null, null];
    }

    /**
     * @param string|array $data
     * @param int               $status
     *
     * @return JsonResponse
     */
    protected function formatReply($data, $status)
    {
        $response = Response::json($data, $status);
        return $response;
    }
}
