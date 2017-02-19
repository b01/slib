<?php namespace Kshabazz\Slib;

use Exception;

/**
 * Class SlibException
 */
abstract class SlibException extends Exception
{
    /**
     * List of error codes and their corresponding messages.
     *
     * @var array
     */
    protected static $errorMap = [];

    /**
     * Constructor
     *
     * @param numeric $code Error code.
     * @param array $variables To fill in placeholders for \vsprintf.
     */
    public function __construct($code, array $variables = NULL)
    {
        $message = $this->getMessageByCode($code, $variables);
        parent::__construct($message, $code);
    }

    /**
     * Convert error code to human readable text.
     *
     * @param numeric & $code
     * @param array $data
     * @return string
     */
    public function getMessageByCode(& $code, array $data = null)
    {
        // If we do not use a reference, then that defeats the purpose of
        // making the $errorMap a static property.
        $map = &static::getErrorMap();

        // When you can't find the code, use a default one.
        if (!\array_key_exists($code, $map)) {
            $code = static::UNKNOWN;
        }

        if (\is_array($data) && count($data) > 0) {
            return \vsprintf($map[$code], $data);
        }

        return $map[$code];
    }

    /**
     * Since the error map is a static property
     * @return array
     */
    static protected function &getErrorMap()
    {
        return static::$errorMap;
    }
}
?>
