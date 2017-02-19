<?php namespace Kshabazz\Slib\Tests\Mocks;

use Kshabazz\Slib\SlibException;

/**
 * Aid for testing SlibException.
 *
 * @author Khalifah
 */
class Except extends SlibException
{
    const TEST_1 = 1;

    const TEST_2 = 2;

    protected $errorMap = [
        self::TEST_1 => 'no placeholders.',
        self::TEST_1 => '%s',
    ];
}
