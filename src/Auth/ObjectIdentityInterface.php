<?php namespace Neomerx\Core\Auth;

/**
 * Interface could be used to identify object by class, class and ID or even object properties.
 *
 * @package Neomerx\Core
 */
interface ObjectIdentityInterface
{
    /**
     * Get object identifier.
     *
     * @return int
     */
    public function getIdentifier();

    /**
     * Get object type.
     *
     * @return string
     */
    public function getTypeName();
}
