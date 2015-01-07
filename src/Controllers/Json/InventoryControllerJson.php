<?php namespace Neomerx\Core\Controllers\Json;

use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Variant;
use \Neomerx\Core\Models\Warehouse;
use \Neomerx\Core\Api\Facades\Inventory;
use \Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Input;
use \Neomerx\Core\Controllers\BaseController;
use \Illuminate\Support\Facades\Response;
use \Neomerx\Core\Converters\ConverterInterface;
use \Neomerx\Core\Api\Inventory\InventoryInterface;
use \Neomerx\Core\Exceptions\InvalidArgumentException;
use \Neomerx\Core\Converters\InventoryConverterGeneric;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class InventoryControllerJson extends BaseController
{
    /**
     * @var InventoryInterface
     */
    private $apiFacade;

    /**
     * Constructor
     */
    public function __construct()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->apiFacade = App::make(Inventory::INTERFACE_BIND_NAME);
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function show($warehouseCode, $sku)
    {
        return $this->tryAndCatchWrapper('showImpl', [$warehouseCode, $sku]);
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function increment($warehouseCode, $sku)
    {
        $quantity  = Input::get('quantity');
        return $this->tryAndCatchWrapper('incrementImpl', [$warehouseCode, $sku, $quantity]);
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function decrement($warehouseCode, $sku)
    {
        $quantity  = Input::get('quantity');
        return $this->tryAndCatchWrapper('decrementImpl', [$warehouseCode, $sku, $quantity]);
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function makeReserve($warehouseCode, $sku)
    {
        $quantity  = Input::get('quantity');
        return $this->tryAndCatchWrapper('makeReserveImpl', [$warehouseCode, $sku, $quantity]);
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     *
     * @return \Illuminate\Http\JsonResponse
     */
    final public function releaseReserve($warehouseCode, $sku)
    {
        $quantity  = Input::get('quantity');
        return $this->tryAndCatchWrapper('releaseReserveImpl', [$warehouseCode, $sku, $quantity]);
    }

    /**
     * @param string|array|null $data
     * @param int               $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function formatReply($data, $status)
    {
        $response = Response::json($data, $status);
        return $response;
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     *
     * @return array
     */
    protected function showImpl($warehouseCode, $sku)
    {
        $inventory = $this->apiFacade->read(
            $this->getModelByCode(Variant::BIND_NAME, $sku),
            $this->getModelByCode(Warehouse::BIND_NAME, $warehouseCode)
        );
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var ConverterInterface $converter */
        $converter = App::make(InventoryConverterGeneric::BIND_NAME);
        return [$converter->convert($inventory), null];
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     * @param int    $quantity
     *
     * @return array
     */
    protected function incrementImpl($warehouseCode, $sku, $quantity)
    {
        $this->apiFacade->increment(
            $this->getModelByCode(Variant::BIND_NAME, $sku),
            $this->getModelByCode(Warehouse::BIND_NAME, $warehouseCode),
            $quantity
        );
        return [null, null];
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     * @param int    $quantity
     *
     * @return array
     */
    protected function decrementImpl($warehouseCode, $sku, $quantity)
    {
        $this->apiFacade->decrement(
            $this->getModelByCode(Variant::BIND_NAME, $sku),
            $this->getModelByCode(Warehouse::BIND_NAME, $warehouseCode),
            $quantity
        );
        return [null, null];
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     * @param int    $quantity
     *
     * @return array
     */
    protected function makeReserveImpl($warehouseCode, $sku, $quantity)
    {
        $quantity !== null ?: S\throwEx(new InvalidArgumentException('quantity'));
        (settype($quantity, 'int') and $quantity > 0) ?: S\throwEx(new InvalidArgumentException('quantity'));

        $this->apiFacade->makeReserve(
            $this->getModelByCode(Variant::BIND_NAME, $sku),
            $this->getModelByCode(Warehouse::BIND_NAME, $warehouseCode),
            $quantity
        );

        return [null, null];
    }

    /**
     * @param string $warehouseCode
     * @param string $sku
     * @param int    $quantity
     *
     * @return array
     */
    protected function releaseReserveImpl($warehouseCode, $sku, $quantity)
    {
        $quantity !== null ?: S\throwEx(new InvalidArgumentException('quantity'));
        (settype($quantity, 'int') and $quantity > 0) ?: S\throwEx(new InvalidArgumentException('quantity'));

        $this->apiFacade->releaseReserve(
            $this->getModelByCode(Variant::BIND_NAME, $sku),
            $this->getModelByCode(Warehouse::BIND_NAME, $warehouseCode),
            $quantity
        );

        return [null, null];
    }
}
