<?php namespace Kshabazz\Slib\Tests;

/**
 * Class HttpServiceTest
 *
 * @package \Kshabazz\Slib\Tests
 */
class HttpServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \GuzzleHttp\Client|\PHPUnit_Framework_MockObject_MockObject */
    private $mockHttpClient;

    /** @var \GuzzleHttp\Message\Request|\PHPUnit_Framework_MockObject_MockObject */
    private $mockRequest;

    /** @var \GuzzleHttp\Message\Response|\PHPUnit_Framework_MockObject_MockObject */
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
        $service = new MockService($this->mockHttpClient, '', '', '');

        $this->assertInstanceOf(Service::class, $service);
    }

    /**
     * @covers ::send
     * @uses \Venture\RateGrabber\Service::__construct
     */
    public function testCanSendRequest()
    {
        $this->mockHttpClient->expects($this->once())
            ->method('createRequest')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest)
            ->willReturn(1234);

        $service = new MockService($this->mockHttpClient, '', '', '');

        $actual = $service->doSend('test', '/testUrl', [], 'test body');

        $this->assertEquals(1234, $actual);
    }

    /**
     * @covers ::send
     * @uses \Venture\RateGrabber\Service::__construct
     * @expectedException \Venture\RateGrabber\RateGrabberException
     */
    public function testCanCatchExceptionWhenSendBlowsUp()
    {
        $this->mockHttpClient->expects($this->once())
            ->method('createRequest')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest)
            ->will($this->throwException(new \Exception('testing')));

        $service = new MockService($this->mockHttpClient, '', '', '');

        $service->doSend('test', '/testUrl', [], 'test body');
    }

    /**
     * @covers ::getLastRequest
     * @uses \Venture\RateGrabber\Service::__construct
     */
    public function testCanCatchLastRequest()
    {
        $this->mockHttpClient->expects($this->once())
            ->method('createRequest')
            ->willReturn($this->mockRequest);

        $this->mockHttpClient->expects($this->once())
            ->method('send')
            ->with($this->mockRequest);

        $service = new MockService($this->mockHttpClient, '', '', '');

        $service->doSend('test', '/testUrl', [], 'test body');

        $actual = $service->getLastRequest();

        $this->assertEquals($this->mockRequest, $actual);
    }
}
