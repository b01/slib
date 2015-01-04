<?php namespace Kshabazz\Test\Slib;
/**
 * Created by PhpStorm.
 * User: Khalifah
 * Date: 1/4/14
 * Time: 9:24 AM
 */
use Kshabazz\Slib\SqlClient;

/**
 * Class SqlClientTest
 * @package kshabazz\d3a\test
 */
class SqlClientTest extends \PHPUnit_Framework_TestCase
{
	private $pdo;

	public function setUp()
	{
		$this->pdo = new \PDO(
			'mysql:host=localhost;dbname=test;charset=utf8',
			'travis',
			'',
			[ // Show human readable errors from the database server when they occur.
			  \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			  \PDO::ATTR_EMULATE_PREPARES => FALSE,
			  \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
			]
		);
	}

	/**
	 * Get IP addresss.
	 */
	public function test_retrieving_ip_address()
	{
		$sql = new SqlClient( $this->pdo, '127.0.0.1' );
		// once the IP goes in, it should never change, no setter for IP address.
		$this->assertEquals( '127.0.0.1', $sql->ipAddress(), 'Invalid IP address returned.' );
	}

	/**
	 * Test inserting data into the DB.
	 *
	 * The residual effects are that pdoQuery,
	 */
	public function test_insert_query()
	{
		$sql = new SqlClient( $this->pdo );
		$column1Value = 'test column 1';
		$truncateStmt = $sql->pdo()->prepare( 'TRUNCATE TABLE `test_1`' );
		$insertStmt = $sql->pdo()->prepare( "INSERT INTO `test_1` (`column1`) VALUES('{$column1Value}');" );
		$selectStmt = $sql->pdo()->prepare( 'SELECT `id`, `column1` FROM `test_1`' );

		// clear any data out of the table.
		$sql->pdoQuery( $truncateStmt, FALSE );
		// insert some test data.
		$sql->pdoQuery( $insertStmt, FALSE );
		// retrieve the test data.
		$result = $sql->pdoQuery( $selectStmt );

		$this->assertEquals( '1', $result[0]['id'], '' );
	}

	/**
	 * Test the catch block of pdoQuery.
	 *
	 * @expectedException \Exception
	 * @expectedExceptionMessage A PDO Error has occurred
	 */
	public function test_pdoQuery_catch_block()
	{
		$sql = new SqlClient( $this->pdo );
		// invalid column name given.
		$selectStmt = $sql->pdo()->prepare( "SELECT `column1` FROM `test_1` WHERE `id` = :id" );
		// run invalid query that should cause an error.
		$sql->pdoQuery( $selectStmt );

		$this->fail( 'No exception thrown like expected.' );
	}


	/**
	 * Test select data from the DB.
	 */
	public function test_select_method()
	{
		$sql = new SqlClient(  $this->pdo );
		$column1Value = 'test column 1';
		$truncateStmt = $sql->pdo()->prepare( 'TRUNCATE TABLE `test_1`' );
		$insertStmt = $sql->pdo()->prepare( "INSERT INTO `test_1` (`column1`) VALUES('{$column1Value}');" );
		$selectQuery = 'SELECT `id`, `column1` FROM `test_1`';

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
		$sql = new SqlClient(  $this->pdo );
		$selectQuery = 'SELECT `id`, `column1` FROM `test_1` WHERE `id` = ?';
		// retrieve the test data.
		$sql->select( $selectQuery );
		$this->fail( 'No exception thrown like expected.' );
	}

	/**
	 * Test pdoQueryBind method.
	 */
	public function test_pdoQueryBind_method()
	{
		$sql = new SqlClient( $this->pdo );
		$column1Val = 'test column 1';
		$sql->pdo()->prepare( 'TRUNCATE TABLE `test_1`' );
		$sql->pdoQueryBind(
			"INSERT INTO `test_1` (`column1`) VALUES('{:column1Val}');",
			[
				'column1Val' => [ $column1Val, \PDO::PARAM_STR ]
			]
		);
		// retrieve the test data and assert.
		$results = $sql->pdoQueryBind( 'SELECT `id`, `column1` FROM `test_1`' );
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
		$sql = new SqlClient( $this->pdo );
		$column1Val = 'test column 1';
		// retrieve the test data and assert.
		$results = $sql->pdoQueryBind( 'SELECT `id`, `column1` FROM `test_1` WHERE `id` = ?' );
		$this->assertEquals( $column1Val, $results[0]['column1'], 'Invalid results returned.' );
	}
}

// Writing below this line can cause headers to be sent before intended ?>