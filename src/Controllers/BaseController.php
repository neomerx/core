<?php namespace Neomerx\Core\Controllers;

use \Exception;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Models\BaseModel;
use \Illuminate\Routing\Controller;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Log;
use \Illuminate\Support\Facades\Config;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\SelectByCodeInterface;
use \Neomerx\Core\Exceptions\ValidationException;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Exceptions\ResourceNotFoundException;
use \Neomerx\Core\Exceptions\Exception as NeomerxBaseException;
use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class BaseController extends Controller
{
    /**
     * Wraps class calls with try catch.
     *
     * @param string $methodName
     * @param array  $parameters
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final protected function tryAndCatchWrapper($methodName, array $parameters)
    {
        $errorReply = [];
        $codeOnError = SymfonyResponse::HTTP_BAD_REQUEST;
        try {

            list($data, $code) = call_user_func_array([$this, $methodName], $parameters);
            $code = ($code ? $code : SymfonyResponse::HTTP_OK);
            return $this->formatReply($data, $code);

        } catch (ResourceNotFoundException $e) {

            $errorReply['type'] = 'invalid_request_error';
            $errorReply['message'] = $e->getMessage();
            $errorReply['param'] = $e->getName();
            $errorReply['value'] = $e->getValue();
            $codeOnError = SymfonyResponse::HTTP_NOT_FOUND;

        } catch (InvalidArgumentException $e) {

            $errorReply['type'] = 'invalid_request_error';
            $errorReply['message'] = $e->getMessage();
            $errorReply['param'] = $e->getName();

        } catch (ValidationException $e) {

            $errorReply['type'] = 'validation_error';
            $errorReply['message'] = $e->getMessage();
            $errorReply['validation'] = $e->getValidator()->getMessageBag()->toArray();

        } catch (NeomerxBaseException $e) {

            $errorReply['type'] = 'api_error';
            $errorReply['message'] = $e->getMessage();

            /** @noinspection PhpUndefinedMethodInspection */
            Log::error('Api error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());

        } catch (Exception $e) {

            $message = $e->getMessage();

            /** @noinspection PhpUndefinedMethodInspection */
            Log::critical($message);

            // Hide error message in production
            /** @noinspection PhpUndefinedMethodInspection */
            if (!Config::get('app.debug')) {
                $message = trans('nm::errors.unexpected_error');
            }

            $errorReply['type'] = 'api_error';
            $errorReply['message'] = $message;
        }

        return $this->formatReply($errorReply, $codeOnError);
    }

    /**
     * @param string|array $data
     * @param int          $status
     *
     * @return mixed
     */
    abstract protected function formatReply($data, $status);

    /**
     * Get model by ID.
     *
     * @param string $modelBindName
     * @param int    $resourceId
     *
     * @return mixed
     */
    protected function getModelById($modelBindName, $resourceId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $resource = App::make($modelBindName)->newQuery()->findOrFail($resourceId);
        Permissions::check($resource, Permission::view());
        return $resource;
    }

    /**
     * Get model by ID.
     *
     * @param string $modelBindName
     * @param string $code
     *
     * @return mixed
     */
    protected function getModelByCode($modelBindName, $code)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $model = App::make($modelBindName);
        $model instanceof SelectByCodeInterface ?: S\throwEx(new InvalidArgumentException('modelBindName'));

        /** @var SelectByCodeInterface $model */

        /** @var BaseModel $resource */
        $resource = $model->selectByCode($code)->firstOrFail();
        Permissions::check($resource, Permission::view());
        return $resource;
    }
}
