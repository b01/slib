<?php namespace Kshabazz\Slib;

/**
 * Class JsonModelException
 */
class JsonModelException extends SlibException
{
    const BAD_PROPERTY_TYPE = 1;

    const PROPERTY_EMPTY = 2;

    /**
     * List of error codes and their corresponding messages.
     *
     * @var array
     */
    protected static $errorMap = [
        self::BAD_PROPERTY_TYPE => '%s should be of type "%s", actual type is %s.',
        self::PROPERTY_EMPTY => '%s::%s has been set to null, needs to be "%s".'
    ];
}
?>