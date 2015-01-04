<?php namespace Kshabazz\Tests\Slib;
/**
 * Created by PhpStorm.
 * User: Khalifah
 * Date: 1/5/14
 * Time: 2:06 PM
 */

/**
 * Class _Tool
 * @package Kshabazz\Slib\Test
 */
class FunctionTest extends \PHPUnit_Framework_TestCase
{
	private
		/** @var string */
		$fixtures;

	public function setUp()
	{
		$this->fixtures = FIXTURES_PATH . DIRECTORY_SEPARATOR;
	}

	/**
	 * Test converting a string to camel-case.
	 */
	public function test_camelCase_function()
	{
		$lowerCamelCased = \Kshabazz\Slib\camelCase( 'test-me' );
		$upperCamelCased = \Kshabazz\Slib\camelCase( 'test-me', TRUE );
		$this->assertEquals( 'testMe', $lowerCamelCased, 'Invalid CamelCase returned.' );
		$this->assertEquals( 'TestMe', $upperCamelCased, 'Invalid camelCase returned.' );
	}

	/**
	 * Check PHP version
	 */
	public function test_check_valid_php_version_function()
	{
		$requirementMet = \Kshabazz\Slib\checkPhpVersion( 5, 4, 3 );
		$this->assertTrue( $requirementMet, 'Requirements were not met, checkPhpVersion failed.' );
	}

	/**
	 * Check PHP version
\	 */
	public function test_check_invalid_php_version_function()
	{
		$phpVersion = phpversion();
		$this->setExpectedException(
			'Exception',
			"Your PHP version is '{$phpVersion}'. The minimum required PHP version is '6.6.4'. You'll need to upgrade in order to use this application."
		);
		\Kshabazz\Slib\checkPhpVersion( 6, 6, 4 );
		$this->fail( 'No \Exception throw as expected.' );
	}

	/**
	 * Test convertToClassName.
	 */
	public function test_convertToClassName_function()
	{
		$camelCased = \Kshabazz\Slib\convertToClassName( 'test-me.php' );
		$this->assertEquals( 'TestMe', $camelCased, 'Invalid class name returned.' );
	}

	/**
	 * Test getHtmlInnerBody.
	 */
	public function test_getHtmlInnerBody_function()
	{
		$bodyText = \Kshabazz\Slib\getHtmlInnerBody( '<body>test</body>' );
		$this->assertEquals( 'test', $bodyText, 'Invalid body text returned.' );
	}

	/**
	 * Test includeContents.
	 */
	public function test_includeContents_function()
	{
		$contents = \Kshabazz\Slib\includeContents( $this->fixtures . 'test.php' );
		$this->assertEquals( 'test', $contents, 'Could not get contents from test file.' );
	}

	/**
	 * Test includeContents.
	 */
	public function test_includeContents_of_non_existing_file()
	{
		$contents = \Kshabazz\Slib\includeContents( $this->fixtures . 'non-existing-file.php' );
		$this->assertFalse( $contents, 'Unexpected file content from non-existant file.' );
	}

	/**
	 * Test isArray.
	 */
	public function test_isArray_function()
	{
		$ary = \Kshabazz\Slib\isArray([ 'test' ]);
		$this->assertTrue( $ary, 'Not an array.' );
	}

	/**
	 * Test isString.
	 */
	public function test_isString_function()
	{
		$str = \Kshabazz\Slib\isString( 'test' );
		$this->assertTrue( $str, 'Not a string.' );
	}

	/**
	 * Test loadJsonFile.
	 */
	public function test_loadJsonFile_function()
	{
		$ary = \Kshabazz\Slib\loadJsonFile( $this->fixtures . 'test.json' );
		$this->assertEquals( '1234', $ary['test'], 'Could not load JSON data from test file.' );
	}

	/**
	 * Test fail of loadJsonFile.
	 */
	public function test_fail_of_loadJsonFile_function()
	{
		$ary = \Kshabazz\Slib\loadJsonFile( $this->fixtures . 'empty.json' );
		$this->assertEquals( 0, \count($ary), 'Unexpected data loaded from empty JSON test file.' );
	}

	/**
	 * Test saveFile.
	 */
	public function test_randomElementsFromArray_function()
	{
		$source = [ 1, 2, 3, 4 ];
		$item1 = \Kshabazz\Slib\randomElementsFromArray( $source );
		$this->assertTrue(
			\in_array($item1, $source, TRUE),
			'Could not assert that a Random element from source array was returned.'
		);
	}

	/**
	 * Test saveFile.
	 */
	public function test_saveFile_function()
	{
		// todo: change to temp space.
		$tempDir = $this->fixtures . '/bunker';
		$file = $tempDir . '/save-test-file.txt';
		$saved = \Kshabazz\Slib\saveFile( $file, 'test' );
		$this->assertEquals( 4, $saved, 'Unable to save file.' );
		$this->assertFileExists( $file, 'Unexpected data loaded from empty JSON test file.' );
		\unlink( $file );
		\rmdir( $tempDir );
	}

	/**
	 * Verify the quantity returned from UniqueRandomNumbersWithinRange is correct.
	 */
	public function test_quantity_returned_from_UniqueRandomNumbersWithinRange_function()
	{
		$randomNumbers = \Kshabazz\Slib\UniqueRandomNumbersWithinRange( 3, 5, 3 );
		$this->assertEquals( 3, \count($randomNumbers), 'Invalid Quantity of random numbers returned.' );
	}

	/**
	 * Verify the values are unique returned from UniqueRandomNumbersWithinRange is correct.
	 */
	public function test_unique_values_returned_from_UniqueRandomNumbersWithinRange_function()
	{
		$randomNumbers = \Kshabazz\Slib\UniqueRandomNumbersWithinRange( 3, 5, 3 );
		$item1 = $randomNumbers[ 0 ];
		$item2 = $randomNumbers[ 1 ];
		$item3 = $randomNumbers[ 2 ];
		$this->assertNotEquals( $item1, $item2, 'Array item 1 and item 2 are equal.' );
		$this->assertNotEquals( $item1, $item3, 'Array item 1 and item 3 are equal.' );
		$this->assertNotEquals( $item2, $item3, 'Array item 2 and item 3 are equal.' );
	}

	/**
	 * Test setting an invalid quantity.
	 *
	 * @expectedException \Exception
	 * @expectedExceptionMessage Quantity of random numbers requested has to be <= ((max - min) + 1).
	 */
	public function test_invalid_quantity_from_UniqueRandomNumbersWithinRange_function()
	{
		\Kshabazz\Slib\UniqueRandomNumbersWithinRange( 3, 5, 4 );
		$this->fail( 'No \Exception throw as expected.' );
	}
}

// Writing below this line can cause headers to be sent before intended ?>