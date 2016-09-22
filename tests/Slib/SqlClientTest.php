<?php namespace Kshabazz\Test\Slib;

use Kshabazz\Slib\SqlClient;
use PDO;
use PDOStatement;

/**
 * Class SqlClientTest
 *
 * @package \Kshabazz\Test\Slib
 * @coversDefaultClass \Kshabazz\Slib\SqlClient
 */
class SqlClientTest extends \PHPUnit_Framework_TestCase
{
	private
        /** @var \PDO */
        $mockPdo,
        /** @var \PDOStatement */
        $mockStmt,
        /** @var string */
		$table;

	public function setUp()
	{
	    $this->mockPdo = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockStmt = $this->getMockBuilder(PDOStatement::class)
        ->disableOriginalConstructor()
        ->getMock();

		$this->table = 'slib_test';
	}

	/**
	 * Get IP addresss.
     * @covers ::ipAddress
     * @uses \Kshabazz\Slib\SqlClient::__construct
	 */
	public function test_retrieving_ip_address()
	{
		$sql = new SqlClient( $this->mockPdo, '127.0.0.1' );
		// once the IP goes in, it should never change, no setter for IP address.
		$this->assertEquals( '127.0.0.1', $sql->ipAddress(), 'Invalid IP address returned.' );
	}

    /**
     * @covers ::pdoQuery
     * @uses \Kshabazz\Slib\SqlClient::__construct
     */
    public function test_can_run_statement_success_no_results()
    {
        $fixture = 'test';

        $this->mockStmt->expects($this->once())
            ->method('execute')
            ->willReturn($fixture);

        $this->mockStmt->expects($this->once())
            ->method('closeCursor');

        $sql = new SqlClient($this->mockPdo);

        $actual = $sql->pdoQuery($this->mockStmt, false);

        $this->assertEquals($fixture, $actual);
    }

    /**
     * @covers ::pdoQuery
     * @uses \Kshabazz\Slib\SqlClient::__construct
     */
    public function test_can_run_statement_success_with_results()
    {
        $fixture = ['test'];

        $this->mockStmt->expects($this->once())
            ->method('execute')
            ->willReturn('1');

        $this->mockStmt->expects($this->once())
            ->method('closeCursor');

        $this->mockStmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn($fixture);

        $sql = new SqlClient($this->mockPdo);

        $actual = $sql->pdoQuery($this->mockStmt, true);

        $this->assertEquals($fixture[0], $actual[0]);
    }

	/**
	 * Test the catch block of pdoQuery.
	 *
	 * @expectedException \Exception
	 * @expectedExceptionMessage A PDO Error has occurred
	 */
	public function test_pdoQuery_catch_block()
	{
	    $this->markTestIncomplete();
		$sql = new SqlClient( $this->mockPdo );
		// invalid column name given.
		$selectStmt = $sql->pdo()->prepare( "SELECT `column1` FROM `{$this->table}` WHERE `id` = :id" );
		// run invalid query that should cause an error.
		$sql->pdoQuery( $selectStmt );

		$this->fail( 'No exception thrown like expected.' );
	}


	/**
	 * Test select data from the DB.
	 */
	public function test_select_method()
	{
	    $this->markTestIncomplete();
		$sql = new SqlClient( $this->mockPdo );
		$column1Value = 'test column 1';
		$truncateStmt = $sql->pdo()->prepare( "TRUNCATE TABLE `{$this->table}`" );
		$insertStmt = $sql->pdo()->prepare( "INSERT INTO `{$this->table}` (`column1`) VALUES('{$column1Value}');" );
		$selectQuery = "SELECT `id`, `column1` FROM `{$this->table}`";

		// clear any data out of the table.
		$sql->pdoQuery( $truncateStmt, FALSE );
		// insert some test data.
		$sql->pdoQuery( $insertStmt, FALSE );
		// retrieve the test data.
		$result = $sql->select( $selectQuery );
		$this->assertEquals( '1', $result[0]['id'], '' );
	}

	/**
	 * Test invalid select statement.
	 *
	 * @expectedException \Exception
	 * @expectedExceptionMessage Select statement failed
	 */
	public function test_invalid_use_of_select_method()
	{
        $this->markTestIncomplete();
		$sql = new SqlClient( $this->mockPdo );
		$selectQuery = "SELECT `id`, `column1` FROM `{$this->table}` WHERE `id` = ?";
		// retrieve the test data.
		$sql->select( $selectQuery );
		$this->fail( 'No exception thrown like expected.' );
	}

	/**
	 * Test pdoQueryBind method.
	 */
	public function test_pdoQueryBind_method()
	{
        $this->markTestIncomplete();
		$sql = new SqlClient( $this->mockPdo );
		$column1Val = 'test column 1';
		$sql->pdo()->prepare( 'TRUNCATE TABLE `{$this->table}`' );
		$sql->pdoQueryBind(
			"INSERT INTO `{$this->table}` (`column1`) VALUES('{:column1Val}');",
			[
				'column1Val' => [ $column1Val, \PDO::PARAM_STR ]
			]
		);
		// retrieve the test data and assert.
		$results = $sql->pdoQueryBind( "SELECT `id`, `column1` FROM `{$this->table}`" );
		$this->assertEquals( $column1Val, $results[0]['column1'], 'Invalid results returned.' );
	}

	/**
	 * Test pdoQueryBind method.
	 *
	 * @expectedException \Exception
	 * @expectedExceptionMessage A PDO Error has occurred
	 */
	public function test_invalid_use_of_pdoQueryBind_method()
	{
        $this->markTestIncomplete();
		$sql = new SqlClient( $this->mockPdo );
		$column1Val = 'test column 1';
		// retrieve the test data and assert.
		$results = $sql->pdoQueryBind( "SELECT `id`, `column1` FROM `{$this->table}` WHERE `id` = ?" );
		$this->assertEquals( $column1Val, $results[0]['column1'], 'Invalid results returned.' );
	}
}

?>