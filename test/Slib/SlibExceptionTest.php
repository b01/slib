<?php namespace Kshabazz\Slib\Tests;

use Kshabazz\Slib\SlibException;
use Kshabazz\Slib\Tests\Mocks\Except;

/**
 * Unit test for class SlibException
 *
 * @coversDefaultClass \Kshabazz\Slib\SlibException
 */
class SlibExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers ::__construct
     */
    public function testVerifyThatADocMarkdownExceptionCanBeConstructed()
    {
        $exception = new Except(SlibException::UNKNOWN);

        $this->assertInstanceOf(SlibException::class, $exception);
    }

    /**
     * @covers ::getErrorMap
     * @covers ::getMessageByCode
     */
    public function testVerifyThatAnErrorCodeMapsToTheCorrectMessage()
    {
        $error = new Except(Except::TEST_1);
        $expectedCode = Except::TEST_1;
        $expected = $error->getMessageByCode($expectedCode);

        $this->assertEquals($expected, $error->getMessage());
        $this->assertEquals($expectedCode, $error->getCode());
    }

    /**
     * @covers ::getMessageByCode
     */
    public function testGetTheDefaultErrorWhenInvalidCodeIsUsed()
    {
        $error = new Except(-1);
        $expectedCode = Except::TEST_1;
        $expected = $error->getMessageByCode($expectedCode);

        $this->assertEquals($expected, $error->getMessage());
        $this->assertEquals($expectedCode, $error->getCode());
    }

    /**
     * @covers ::getMessageByCode
     */
    public function testGetMessageWithPlaceholdersFilledIn()
    {
        $error = new Except(-1);
        $expectedCode = 2;
        $actual = $error->getMessageByCode($expectedCode, ['test1234', 'test1234']);

        $this->assertContains('test1234', $actual);
    }
}
