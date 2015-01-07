<?php namespace Neomerx\Core\Cache;

trait TagTrait
{
    /**
     * Get tag for an object.
     *
     * @param string $objectType
     * @param string $objectId
     *
     * @return string
     */
    private function composeTag($objectType, $objectId)
    {
        return $objectType . '__' .$objectId;
    }
}
