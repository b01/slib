<?php namespace Kshabazz\Slib\Tools;
/**
 * Tools to help simplify repetitive task.
 * @copyright (c) 2013-2017 Khalifah K. Shabazz
 */

/**
 * Trait Utilities
 *
 * @package \Kshabazz\Slib\Tools
 */
trait Utilities
{
    /**
     * Scrub an entire array of potentially harmful client data with htmlspecialchars.
     *
     * @param array $data
     * @return array
     */
    public function cleanArray(array $data)
    {
        $cleanedArray = [];

        foreach ($data as $key => $value) {
            $cleanedArray[$key] = \is_array($value)
                ? $this->cleanArray($value)
                : $this->getSafeArray($key, $data);
        }

        return $cleanedArray;
    }

    /**
     * Get a value from an array, returning the default value when not present in the array.
     *
     * @param string $key
     * @param array $data
     * @param string|null $default
     * @return mixed|null
     */
    public function getFromArray(string $key, array $data, string $default = null)
    {
        $value = $default;

        if (\array_key_exists($key, $data)) {
            $value = $data[$key];
        }

        return $value;
    }

    /**
     * @param string $property
     * @param object $object
     * @param mixed $default
     * @return null
     */
    public function getFromObject(string $property, $object, $default = null)
    {
        $returnValue = $default;

        if (\is_object($object) && \property_exists($object, $property)) {
            $returnValue = $object->{$property};
        }

        return $returnValue;
    }

    /**
     * Get a value from an array, returning the default value when not present in the array, and stripping HTML tags.
     *
     * @param string $key
     * @param array $data
     * @param string|null $default
     * @return mixed|null
     */
    public function getSafeArray(string $key, array & $data, string $default = null)
    {
        $value = $this->getFromArray($key, $data, $default);

        return \htmlspecialchars($value);
    }

    /**
     * Capture the output of an include statement.
     * Note: Taken from PHP example of include function.
     *
     * @param string $pFilename Name of a PHP file to include.
     * @return mixed
     */
    function includeContents($pFilename)
    {
        $returnValue = false;

        if (\is_file($pFilename)) {
            \ob_start();

            include $pFilename;

            $returnValue = \ob_get_clean();
        }

        return $returnValue;
    }

    /**
     * Check if a variable is an array of length greater than 0.
     *
     * @param mixed $pVariable to be checked.
     * @return bool TRUE is yes, false otherwise.
     */
    function isArray($pVariable)
    {
        return (\is_array($pVariable) && \count($pVariable) > 0);
    }

    /**
     * Check if a variable is a string of length greater than 0.
     *
     * @param mixed $pVariable to be checked.
     * @return bool TRUE is yes, false otherwise.
     */
    function isString($pVariable)
    {
        return (\is_string($pVariable) && \strlen($pVariable) > 0);
    }

    /**
     * Load the attribute map from file.
     *
     * @param string $pFile attribute map file contents.
     * @throw \Exception
     * @return array
     */
    function loadJsonAsArray($pFile)
    {
        $contents = \file_get_contents($pFile);
        $returnValue = \json_decode($contents, true);

        if (!$this->isArray($returnValue)) {
            $returnValue = [];
        }

        return $returnValue;
    }

    /**
     * Print the debug backtrace in the following line format.
     * Format: [className::]functionName( parameters )
     *
     * @param array $backtrace Just pass in the output from \debug_backtrace().
     * @return string
     */
    function formatDebugTrace(array $backtrace)
    {
        $rV = '';

        foreach ($backtrace as $trace) {
            $functionName = $traceStr = $trace['function'];
            $args = \join(', ', $trace['args']);
            $className = empty($trace['class'])
                ? ''
                : $traceStr = $trace['class'] . '::';
            $parameters = \count($trace['args']) > 0
                ? '( ' . $args . ' )'
                : '()';

            $rV .= "\n" . $className . $functionName . $parameters;
        }

        $rV .= "\n";

        return $rV;
    }

    /**
     * Random x elements from an array.
     *
     * @param array $pSource Source, array which to pull elements from.
     * @param int $pQuantity Number of elements to retrieve from the array.
     * @return array
     */
    function randomElementsFromArray(array $pSource, int $pQuantity = 1) : array
    {
        \shuffle($pSource);

        return \array_slice($pSource, 0, $pQuantity);
    }

    /**
     * Random x element from an array.
     *
     * @param array $pSource Source, array which to pull elements from.
     * @param int $pQuantity Number of elements to retrieve from the array.
     * @return mixed
     */
    function randomElementFromArray(array $pSource, int $pQuantity = 1) : int
    {
        return $this->randomElementsFromArray($pSource, $pQuantity)[0];
    }

    /**
     * Save content to a file; but will also make the directory if it does not exists.
     *
     * @param string $pFileName path.
     * @param string $pContent data to save in the file.
     * @throws \Exception
     * @return bool
     */
    function saveFile($pFileName, $pContent)
    {
        $directory = \dirname($pFileName);

        if (!\is_dir($directory)) {
            try {
                $madeDir = \mkdir($directory, 0755, TRUE);

            } catch (\Exception $err) {
                throw new \Exception("mkdir: Unable make directory '{$directory}'.");
            }
        }

        // Save data to a file.
        $fileSaved = \file_put_contents($pFileName, $pContent); //, \LOCK_EX );

        if ($fileSaved === FALSE) {
            throw new \Exception("file_put_contents: Unable to save file '{$pFileName}'.");
        }

        return $fileSaved;
    }

    /**
     * Generate an array of random numbers within a specified range.
     * @credit Taken from a Stack Overflow answer:
     *  http://stackoverflow.com/questions/5612656/generating-unique-random-numbers-within-a-range-php
     *
     * @param int $pMin bottom range.
     * @param int $pMax top range.
     * @param int $pQuantity number of random elements to return.
     * @throws \Exception
     * @return array of random numbers.
     */
    function uniqueRandomNumbersWithinRange($pMin, $pMax, $pQuantity)
    {
        $numbersAry = range($pMin, $pMax);

        if (count($numbersAry) < $pQuantity) {
            throw new \Exception('Quantity of random numbers requested has to be <= ((max - min) + 1).');
        }

        shuffle($numbersAry);

        return array_slice($numbersAry, 0, $pQuantity);
    }
}
