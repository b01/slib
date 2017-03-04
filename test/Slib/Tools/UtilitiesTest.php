<?php namespace Kshabazz\Slib\Tests\Tools;

use Kshabazz\Slib\Tools\Utilities;

/**
 * Unit test for trait Functions
 *
 * @coversDefaultClass \Kshabazz\Slib\Functions
 */
class UtilitiesTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Kshabazz\Slib\Tools\Functions|\PHPUnit\Framework */
    private $functions;

    /** @var string */
	private $fixtures;

    public function setUp()
    {
        $this->functions = $this->getMockForTrait(Utilities::class);

		$this->fixtures = FIXTURES_PATH;
	}

	/**
	 * Test includeContents.
	 */
	public function test_includeContents_function()
	{
		$contents = $this->functions->includeContents( $this->fixtures . 'test.php' );
		$this->assertEquals( 'test', $contents, 'Could not get contents from test file.' );
	}

	/**
	 * Test includeContents.
	 */
	public function test_includeContents_of_non_existing_file()
	{
		$contents = $this->functions->includeContents( $this->fixtures . 'non-existing-file.php' );
		$this->assertFalse( $contents, 'Unexpected file content from non-existant file.' );
	}

	/**
	 * Test isArray.
	 */
	public function test_isArray_function()
	{
		$ary = $this->functions->isArray([ 'test' ]);
		$this->assertTrue( $ary, 'Not an array.' );
	}

	/**
	 * Test isString.
	 */
	public function test_isString_function()
	{
		$str = $this->functions->isString( 'test' );
		$this->assertTrue( $str, 'Not a string.' );
	}

	/**
	 * Test loadJsonAsArray.
	 */
	public function testLoadJsonAsArrayUtility()
	{
		$ary = $this->functions->loadJsonAsArray( $this->fixtures . 'test.json' );
		$this->assertEquals( '1234', $ary['test'], 'Could not load JSON data from test file.' );
	}

	/**
	 * Test fail of loadJsonAsArray.
	 */
	public function testWillFailToLoadJsonAsArrayWithEmptyJson()
	{
		$ary = $this->functions->loadJsonAsArray( $this->fixtures . 'empty.json' );
		$this->assertEquals( 0, \count($ary), 'Unexpected data loaded from empty JSON test file.' );
	}

	/**
	 * Test saveFile.
	 */
	public function test_randomElementsFromArray_function()
	{
		$source = [ 1, 2, 3, 4 ];
		$item1 = $this->functions->randomElementsFromArray( $source );
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
		$saved = $this->functions->saveFile( $file, 'test' );
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
		$randomNumbers = $this->functions->UniqueRandomNumbersWithinRange( 3, 5, 3 );
		$this->assertEquals( 3, \count($randomNumbers), 'Invalid Quantity of random numbers returned.' );
	}

	/**
	 * Verify the values are unique returned from UniqueRandomNumbersWithinRange is correct.
	 */
	public function test_unique_values_returned_from_UniqueRandomNumbersWithinRange_function()
	{
		$randomNumbers = $this->functions->UniqueRandomNumbersWithinRange( 3, 5, 3 );
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
		$this->functions->UniqueRandomNumbersWithinRange( 3, 5, 4 );

		$this->fail( 'No \Exception throw as expected.' );
	}
}
