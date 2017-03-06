<?php namespace Kshabazz\Slib\Tests\Tools;

use Kshabazz\Slib\Tools\Utilities;
use const Kshabazz\Slib\Tests\FIXTURES_PATH;

/**
 * Unit test for trait Functions
 *
 * @coversDefaultClass \Kshabazz\Slib\Tools\Utilities
 */
class UtilitiesTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Kshabazz\Slib\Tools\Utilities|\PHPUnit_Framework_MockObject_MockObject */
    private $utilities;

    /** @var string */
	private $fixtures;

    public function setUp()
    {
        $this->utilities = $this->getMockForTrait(Utilities::class);

		$this->fixtures = FIXTURES_PATH;
	}

    /**
     * @covers ::getFromArray
     */
    public function testGetFromArrayWillReturnValueFromArray()
    {
        $fixture = ['test' => 1234];

        $actual = $this->utilities->getFromArray('test', $fixture);

        $this->assertEquals(1234, $actual);
    }

    /**
     * @covers ::getFromArray
     */
    public function testGetFromArrayWillReturnDefaultValueWhenValueIsNotInArray()
    {
        $fixture = [];

        $actual = $this->utilities->getFromArray('test', $fixture, 1234);

        $this->assertEquals(1234, $actual);
    }

    /**
     * @covers ::getSafeArray
     * @uses \Kshabazz\Slib\Tools\Utilities::getFromArray
     */
    public function testGetSafeArrayWillEncodeHtmlTagsPresent()
    {
        $fixture = ['test' => '123<br />567'];

        $actual = $this->utilities->getSafeArray('test', $fixture);

        $this->assertEquals('123&lt;br /&gt;567', $actual);
    }

    /**
     * @covers ::cleanArray
     * @uses \Kshabazz\Slib\Tools\Utilities::getSafeArray
     * @uses \Kshabazz\Slib\Tools\Utilities::getFromArray
     */
    public function testCanCleanSpecialCharsFromArrayElements()
    {
        $fixture = ['test' => '123<br />567', 'test2' => '123<script>567'];

        $actual = $this->utilities->cleanArray($fixture);

        $this->assertEquals('123&lt;br /&gt;567', $actual['test']);
        $this->assertEquals('123&lt;script&gt;567', $actual['test2']);
    }

	/**
	 * @covers ::includeContents
	 */
	public function test_includeContents_function()
	{
		$contents = $this->utilities->includeContents( $this->fixtures . 'test.php' );
		$this->assertEquals( 'test', $contents, 'Could not get contents from test file.' );
	}

	/**
	 * @covers ::includeContents
	 */
	public function test_includeContents_of_non_existing_file()
	{
		$contents = $this->utilities->includeContents( $this->fixtures . 'non-existing-file.php' );
		$this->assertFalse( $contents, 'Unexpected file content from non-existant file.' );
	}

	/**
	 * @covers ::isArray
	 */
	public function test_isArray_function()
	{
		$ary = $this->utilities->isArray([ 'test' ]);
		$this->assertTrue( $ary, 'Not an array.' );
	}

	/**
	 * @covers ::isString
	 */
	public function test_isString_function()
	{
		$str = $this->utilities->isString( 'test' );
		$this->assertTrue( $str, 'Not a string.' );
	}

	/**
	 * @covers ::loadJsonAsArray
	 */
	public function testLoadJsonAsArrayUtility()
	{
		$ary = $this->utilities->loadJsonAsArray( $this->fixtures . 'test.json' );
		$this->assertEquals( '1234', $ary['test'], 'Could not load JSON data from test file.' );
	}

	/**
	 * @covers ::loadJsonAsArray
	 */
	public function testWillFailToLoadJsonAsArrayWithEmptyJson()
	{
		$ary = $this->utilities->loadJsonAsArray( $this->fixtures . 'empty.json' );
		$this->assertEquals( 0, \count($ary), 'Unexpected data loaded from empty JSON test file.' );
	}

	/**
	 * @covers ::saveFile
     * @uses \Kshabazz\Slib\Tools\Utilities::isArray
	 */
	public function test_randomElementsFromArray_function()
	{
		$source = [ 1, 2, 3, 4 ];
		$item1 = $this->utilities->randomElementsFromArray( $source );
		$this->assertTrue(
			\in_array($item1, $source, TRUE),
			'Could not assert that a Random element from source array was returned.'
		);
	}

	/**
	 * @covers ::saveFile
     * @uses \Kshabazz\Slib\Tools\Utilities::isArray
	 */
	public function test_saveFile_function()
	{
		// todo: change to temp space.
		$tempDir = $this->fixtures . '/bunker';
		$file = $tempDir . '/save-test-file.txt';
		$saved = $this->utilities->saveFile( $file, 'test' );
		$this->assertEquals( 4, $saved, 'Unable to save file.' );
		$this->assertFileExists( $file, 'Unexpected data loaded from empty JSON test file.' );
		\unlink( $file );
		\rmdir( $tempDir );
	}

	/**
	 * @covers ::uniqueRandomNumbersWithinRange
	 */
	public function test_quantity_returned_from_UniqueRandomNumbersWithinRange_function()
	{
		$randomNumbers = $this->utilities->uniqueRandomNumbersWithinRange( 3, 5, 3 );
		$this->assertEquals( 3, \count($randomNumbers), 'Invalid Quantity of random numbers returned.' );
	}

	/**
	 * @covers ::uniqueRandomNumbersWithinRange
	 */
	public function test_unique_values_returned_from_UniqueRandomNumbersWithinRange_function()
	{
		$randomNumbers = $this->utilities->uniqueRandomNumbersWithinRange( 3, 5, 3 );
		$item1 = $randomNumbers[ 0 ];
		$item2 = $randomNumbers[ 1 ];
		$item3 = $randomNumbers[ 2 ];

		$this->assertNotEquals( $item1, $item2, 'Array item 1 and item 2 are equal.' );
		$this->assertNotEquals( $item1, $item3, 'Array item 1 and item 3 are equal.' );
		$this->assertNotEquals( $item2, $item3, 'Array item 2 and item 3 are equal.' );
	}

	/**
     * @covers ::uniqueRandomNumbersWithinRange
	 * @expectedException \Exception
	 * @expectedExceptionMessage Quantity of random numbers requested has to be <= ((max - min) + 1).
	 */
	public function test_invalid_quantity_from_UniqueRandomNumbersWithinRange_function()
	{
		$this->utilities->uniqueRandomNumbersWithinRange( 3, 5, 4 );

		$this->fail( 'No \Exception throw as expected.' );
	}
}
