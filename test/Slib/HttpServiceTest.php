<?php namespace Kshabazz\Slib\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Kshabazz\Slib\HttpService;
use Kshabazz\Slib\Tests\Mocks\MockHttpService;
use PHPUnit\Framework\TestCase;

/**
 * Class HttpServiceTest
 *
 * @package \Kshabazz\Slib\Tests
 * @coversDefaultClass \Kshabazz\Slib\HttpService
 */
class HttpServiceTest extends TestCase
{
    /** @var \GuzzleHttp\Client|\PHPUnit\Framework\MockObject\MockObject */
    private $mockHttpClient;

    /** @var \GuzzleHttp\Psr7\Request|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRequest;

    /** @var \GuzzleHttp\Psr7\Response|\PHPUnit\Framework\MockObject\MockObject */
    private $mockResponse;

    public function setUp()
    {
        $this->mockHttpClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockResponse = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testCanInitialize()
    {
        $service = new MockHttpService($this->mockHttpClient, '', '', '');

        $this->assertInstanceOf(HttpService::class, $service);
    }

    /**
     * @covers ::send
     * @uses \Kshabazz\Slib\HttpService::__construct
     */
    public function testCanSendRequest()
    {
        $this->mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest)
            ->willReturn(1234);

        $service = new MockHttpService($this->mockHttpClient, '', '', '');

        $actual = $service->doSend('test', '/testUrl', [], 'test body');

        $this->assertEquals(1234, $actual);
    }

    /**
     * @covers ::send
     * @uses \Kshabazz\Slib\HttpService::__construct
     * @expectedException \Kshabazz\Slib\SlibException
     */
    public function testCanCatchExceptionWhenSendBlowsUp()
    {
        $this->mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest)
            ->will($this->throwException(new \Exception('testing')));

        $service = new MockHttpService($this->mockHttpClient, '', '', '');

        $service->doSend('test', '/testUrl', [], 'test body');
    }

    /**
     * @covers ::getLastRequest
     * @uses \Kshabazz\Slib\HttpService::__construct
     */
    public function testCanCatchLastRequest()
    {
        $this->mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest);

        $service = new MockHttpService($this->mockHttpClient, '', '', '');

        $service->doSend('test', '/testUrl', [], 'test body');

        $actual = $service->getLastRequest();

        $this->assertEquals($this->mockRequest, $actual);
    }
}
