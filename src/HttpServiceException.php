<?php namespace Kshabazz\Slib;

/**
 * Class HttpServiceException
 * @package \Kshabazz\Slib
 */
class HttpServiceException extends SlibException
{
    const BAD_SERVICE_REQUEST = 1;

    /**
     * List of error codes and their corresponding messages.
     *
     * @var array
     */
    protected static $errorMap = [
        self::BAD_SERVICE_REQUEST => 'HTTP request error: %s',
    ];
}
