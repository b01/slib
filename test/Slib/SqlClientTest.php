<?php namespace Kshabazz\Slib\Tests;

use Exception;
use Kshabazz\Slib\SqlClient;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/**
 * Class SqlClientTest
 *
 * @package \Kshabazz\Test\Slib
 * @coversDefaultClass \Kshabazz\Slib\SqlClient
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class SqlClientTest extends TestCase
{
    private
        /** @var \PDO|\PHPUnit\Framework\MockObject\MockObject */
        $mockPdo,
        /** @var \PDOStatement|\PHPUnit\Framework\MockObject\MockObject */
        $mockStmt,
        /** @var string */
        $table;

    public function setUp()
    {
        $this->mockPdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();

        $this->mockStmt = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();

        $this->table = 'slib_test';
    }

    public function tearDown()
    {
        $this->mockPdo = null;
        $this->mockStmt = null;
        unset($this->table);
    }

    /**
     * @covers ::__construct
     * @covers ::ipAddress
     * @uses \Kshabazz\Slib\SqlClient::__construct
     */
    public function test_retrieving_ip_address()
    {
        $sql = new SqlClient($this->mockPdo, '127.0.0.1');
        // once the IP goes in, it should never change, no setter for IP address.
        $this->assertEquals('127.0.0.1', $sql->ipAddress(), 'Invalid IP address returned.');
    }

    /**
     * @covers ::pdoQuery
     * @uses \Kshabazz\Slib\SqlClient::__construct
     */
    public function test_can_run_statement_success_no_results()
    {
        $fixture = 'test';

        $this->mockStmt->expects($this->once())->method('execute')->willReturn($fixture);

        $this->mockStmt->expects($this->once())->method('closeCursor');

        $sql = new SqlClient($this->mockPdo);

        $actual = $sql->pdoQuery($this->mockStmt, false);

        $this->assertEquals($fixture, $actual);

        $sql = null;
    }

    /**
     * @covers ::pdoQuery
     * @uses \Kshabazz\Slib\SqlClient::__construct
     * @uses \Kshabazz\Slib\Tools\Utilities::isArray
     */
    public function test_can_run_statement_success_with_results()
    {
        $fixture = ['test'];

        $this->mockStmt->expects($this->once())->method('execute')->willReturn('1');

        $this->mockStmt->expects($this->once())->method('closeCursor');

        $this->mockStmt->expects($this->once())->method('fetchAll')->willReturn($fixture);

        $sql = new SqlClient($this->mockPdo);

        $actual = $sql->pdoQuery($this->mockStmt, true);

        $this->assertEquals($fixture[ 0 ], $actual[ 0 ]);
    }

    /**
     * Test the catch block of pdoQuery.
     *
     * @covers ::pdoQuery
     * @expectedException \Exception
     * @expectedExceptionMessage A PDO Error has occurred
     */
    public function test_pdoQuery_catch_block()
    {
        $sql = new SqlClient($this->mockPdo);

        // Force an error.
        $this->mockStmt->expects($this->once())->method('execute')->will($this->throwException(new Exception()));

        // run invalid query that should cause an error.
        $sql->pdoQuery($this->mockStmt);

        $this->fail('No exception thrown like expected.');
    }

    /**
     * @covers ::select
     */
    public function test_select_method()
    {
        $fixtureQuery = 'Test Query';
        $fixture = 'test';

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with($fixtureQuery)
            ->willReturn($this->mockStmt);

        $this->mockStmt->expects($this->once())
            ->method('execute');

        $this->mockStmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([$fixture]);

        $sql = new SqlClient($this->mockPdo);

        // retrieve the test data.
        $result = $sql->select($fixtureQuery);
        $this->assertEquals($fixture, $result[0]);
    }

    /**
     * @covers ::select
     * @uses \Kshabazz\Slib\SqlClient::__construct
     * @expectedException \Exception
     * @expectedExceptionMessage Select statement failed
     */
    public function testSelectWillReplaceExceptionMessageWithCustomMessage()
    {
        $fixtureQuery = 'test';

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with($fixtureQuery)
            ->will($this->throwException(new Exception()));

        $sql = new SqlClient($this->mockPdo);

        $sql->select($fixtureQuery);

        $this->fail('No exception thrown like expected.');
    }

    /**
     * @covers ::pdoQueryBind
     * @uses \Kshabazz\Slib\SqlClient::__construct
     * @uses \Kshabazz\Slib\SqlClient::pdoQuery
     * @uses \Kshabazz\Slib\Tools\Utilities::isArray
     */
    public function test_pdoQueryBind_method()
    {
        $fixtureBindings = [
            'bind1' => [1, 2],
            'bind2' => [3, 4]
        ];
        $fixtureQuery = 'test';

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->with($fixtureQuery)
            ->willReturn($this->mockStmt);

        $this->mockStmt->expects($this->exactly(2))
            ->method('bindValue')
            ->will($this->returnValueMap([
                ['bind1', 1, 2, null],
                ['bind2', 3, 4, null],
            ]));

        $this->mockStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->mockStmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([1234]);

        $sql = new SqlClient($this->mockPdo);

        $actual = $sql->pdoQueryBind($fixtureQuery, $fixtureBindings);

        $this->assertEquals(1234, $actual[0]);
    }

    /**
     * Test pdoQueryBind method.
     *
     * @covers ::pdoQueryBind
     * @expectedException \Exception
     * @expectedExceptionMessage A PDO Error has occurred
     */
    public function testPdoQueryBindMethodWillCatchException()
    {

        $this->mockPdo->expects($this->once())
            ->method('prepare')
            ->will($this->throwException(new Exception()));

        $sql = new SqlClient($this->mockPdo);

        $sql->pdoQueryBind('test');
    }
}
