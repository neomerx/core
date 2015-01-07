<?php namespace Neomerx\Core\Events;

use \Illuminate\Support\Str;
use \Illuminate\Support\Facades\DB;
use \Neomerx\Core\Models\SupplyOrder;
use \Neomerx\Core\Api\Facades\Inventories;
use \Neomerx\Core\Models\SupplyOrderDetails;

class SupplyOrdersHandler
{
    /**
     * Handle event.
     *
     * @param EventArgs $args
     *
     * @return void|false|mixed
     */
    public function handle(EventArgs $args)
    {
        $supplyOrder = $args->getModel();

        // on update and create if status has changed to 'validated' ...
        if ($supplyOrder instanceof SupplyOrder and
            $supplyOrder->status === SupplyOrder::STATUS_VALIDATED and
            Str::endsWith($args->getName(), ['.creating', '.updating']) and
            $supplyOrder->isDirty('status')
        ) {
            // ... add inventory records
            /** @noinspection PhpUndefinedMethodInspection */
            DB::beginTransaction();
            try {

                /** @var SupplyOrderDetails $details */
                foreach ($supplyOrder->details as $details) {
                    $quantity = $details->quantity;
                    /** @noinspection PhpUndefinedFieldInspection */
                    Inventories::increment($details->variant, $supplyOrder->warehouse, $quantity);
                }

                $allExecutedOk = true;

            } finally {
                /** @noinspection PhpUndefinedMethodInspection */
                isset($allExecutedOk) ? DB::commit() : DB::rollBack();
            }
        }
    }
}
