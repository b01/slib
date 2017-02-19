<?php namespace Kshabazz\Slib\Tests\Tools;

use Kshabazz\Slib\Tools\Strings;

/**
 * Class LogTest
 *
 * @coversDefaultClass \Kshabazz\Slib\Tools\Strings
 */
class StringsTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Kshabazz\Slib\Tools\Strings|\PHPUnit\Framework */
    private $strings;

    public function setUp()
    {
        $this->strings = $this->buildTraitMok(Strings::class);
	}

	/**
     * @covers ::camelCase
	 */
	public function test_camelCase_function()
	{
		$lowerCamelCased = $this->strings->camelCase( 'test-me' );
		$upperCamelCased = $this->strings->camelCase( 'test-me', TRUE );

		$this->assertEquals( 'testMe', $lowerCamelCased, 'Invalid CamelCase returned.' );
		$this->assertEquals( 'TestMe', $upperCamelCased, 'Invalid camelCase returned.' );
	}

	/**
     * @covers ::checkPhpVersion
	 */
	public function test_check_valid_php_version_function()
	{
		$requirementMet = $this->strings->checkPhpVersion( 5, 4, 3 );

		$this->assertTrue( $requirementMet );
	}

	/**
     * @covers ::checkPhpVersion
     * @expectedException \Exception
     * @expectedExceptionMessage You'll need to upgrade
\	 */
	public function test_check_invalid_php_version_function()
	{
		$phpVersion = \phpversion();
        $phpVer = explode('.', $phpVersion);
		$this->strings->checkPhpVersion( ($phpVer[0] + 1), $phpVer[1], $phpVer[2] );
	}

	/**
     * @covers ::convertToClassName
	 */
	public function test_convertToClassName_function()
	{
		$camelCased = $this->strings->convertToClassName( 'test-me.php' );

		$this->assertEquals( 'TestMe', $camelCased, 'Invalid class name returned.' );
	}

	/**
     * @covers ::getHtmlInnerBody
	 */
	public function test_getHtmlInnerBody_function()
	{
		$bodyText = $this->strings->getHtmlInnerBody( '<body>test</body>' );

		$this->assertEquals( 'test', $bodyText, 'Invalid body text returned.' );
	}
}
