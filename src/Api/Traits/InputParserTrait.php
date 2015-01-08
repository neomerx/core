<?php namespace Neomerx\Core\Api\Traits;

use \Neomerx\Core\Auth\Permission;
use \Neomerx\Core\Auth\Facades\Permissions;
use \Neomerx\Core\Models\SelectByCodeInterface;

trait InputParserTrait
{
    /**
     * Replace code in input with its ID.
     *
     * Read data from $input with code $inputCode then using $model read ID for it from $readKey field. Then store
     * the ID in $input with key $storeKey.
     *
     * @param array                 &$input
     * @param string                $inputCode
     * @param SelectByCodeInterface $model
     * @param string                $readKey
     * @param string                $storeKey
     */
    private function replaceInputCodeWithId(
        array &$input,
        $inputCode,
        SelectByCodeInterface $model,
        $readKey,
        $storeKey
    ) {
        unset($input[$readKey]);
        if (isset($input[$inputCode])) {
            /** @var \Neomerx\Core\Models\BaseModel $item */
            $item = $model->selectByCode($input[$inputCode])->firstOrFail([$readKey]);
            Permissions::check($item, Permission::view());
            $input[$storeKey] = $item->getAttribute($readKey);
            unset($input[$inputCode]);
        }
    }
}
