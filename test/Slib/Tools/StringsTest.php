<?php namespace Kshabazz\Slib\Tests\Tools;

use Kshabazz\Slib\Tools\Strings;
use PHPUnit\Framework\TestCase;

/**
 * Class StringsTest
 *
 * @package \Kshabazz\Slib\Tests\Tools
 * @coversDefaultClass \Kshabazz\Slib\Tools\Strings
 */
class StringsTest extends TestCase
{
    /** @var \Kshabazz\Slib\Tools\Strings|\PHPUnit\Framework\MockObject\MockObject */
    private $sut;

    public function setUp()
    {
        $this->sut = $this->getMockForTrait(Strings::class);
    }

    /**
     * @covers ::camelCase
     */
    public function test_camelCase_function()
    {
        $lowerCamelCased = $this->sut->camelCase('test-me');
        $upperCamelCased = $this->sut->camelCase('test-me', TRUE);

        $this->assertEquals('testMe', $lowerCamelCased, 'Invalid CamelCase returned.');
        $this->assertEquals('TestMe', $upperCamelCased, 'Invalid camelCase returned.');
    }

    /**
     * @covers ::checkPhpVersion
     */
    public function test_check_valid_php_version_function()
    {
        $requirementMet = $this->sut->checkPhpVersion(5, 4, 3);

        $this->assertTrue($requirementMet);
    }

    /**
     * @covers ::checkPhpVersion
     * @expectedException \Exception
     * @expectedExceptionMessage You'll need to upgrade
     */
    public function test_check_invalid_php_version_function()
    {
        $phpVersion = \phpversion();
        $phpVer = explode('.', $phpVersion);
        $this->sut->checkPhpVersion(($phpVer[0] + 1), $phpVer[1], $phpVer[2]);
    }

    /**
     * @covers ::checkPhpVersion
     * @expectedException \Exception
     */
    public function test_check_php_minor_version()
    {
        $phpVersion = \phpversion();
        $phpVer = explode('.', $phpVersion);
        $this->sut->checkPhpVersion(($phpVer[0]), ($phpVer[1] + 1), $phpVer[2]);
    }

    /**
     * @covers ::checkPhpVersion
     * @expectedException \Exception
     */
    public function test_check_php_release_version()
    {
        $phpVersion = \phpversion();
        $phpVer = explode('.', $phpVersion);
        $this->sut->checkPhpVersion(($phpVer[0]), $phpVer[1], ($phpVer[2] + 1));
    }

    /**
     * @covers ::convertToClassName
     */
    public function test_convertToClassName_function()
    {
        $camelCased = $this->sut->convertToClassName('test-me.php');

        $this->assertEquals('TestMe', $camelCased, 'Invalid class name returned.');
    }

    /**
     * @covers ::getHtmlInnerBody
     */
    public function test_getHtmlInnerBody_function()
    {
        $bodyText = $this->sut->getHtmlInnerBody('<body>test</body>');

        $this->assertEquals('test', $bodyText, 'Invalid body text returned.');
    }
}
