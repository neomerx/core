<?php namespace Neomerx\Core\Auth\Token;

interface TokenManagerInterface
{
    /**
     * Save token with payload.
     *
     * @param string $token
     * @param string $payload Payload saved for the token.
     *
     * @return void
     */
    public function saveToken($token, $payload);

    /**
     * Revoke token.
     *
     * @param string $token
     *
     * @return void
     */
    public function revokeToken($token);

    /**
     * Check if token exists.
     *
     * @param string $token
     *
     * @return bool
     */
    public function hasToken($token);

    /**
     * Get payload for the token.
     *
     * @param string $token
     *
     * @return string
     */
    public function getPayload($token);
}
