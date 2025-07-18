<?php

namespace SpotifyWebApiSdk;

class SpotifyWebAPIException extends \Exception
{
    const TOKEN_EXPIRED = 'The access token expired';
    const INVALID_CLIENT = 'invalid_client';
    const RATE_LIMIT_STATUS = 429;

    /**
     * @return bool
     */
    public function hasExpiredToken(): bool {
        return $this->getMessage() === self::TOKEN_EXPIRED;
    }
    /**
     * @return bool
     */
    public function invalidClient(): bool {
        return $this->getMessage() === self::INVALID_CLIENT;
    }

    /**
     * @return bool
     */
    public function isRateLimited(): bool {
        return $this->getCode() === self::RATE_LIMIT_STATUS;
    }
}
