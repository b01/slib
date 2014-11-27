<?php namespace Kshabazz\Test\Slib;

use \Kshabazz\Slib\HttpClient;

/**
 * Class RequestTest
 *
 * @package \Kshabazz\Test\Slib
 * @coversDefaultClass \Kshabazz\Slib\HttpClient
 */
class HttpClientTest extends \PHPUnit_Framework_TestCase
{
	private
		/** @var \Kshabazz\Slib\HttpClient */
		$httpClient,
		/** @var string */
		$url;

	public function setUp()
	{
		$this->url = 'http://www.example.com';
		$this->httpClient = new HttpClient();
	}

	public function test_initialization()
	{
		$http = new HttpClient();
		$this->assertInstanceOf( '\\Kshabazz\\Slib\\HttpClient', $http );
	}

	/**
	 * @covers ::setRequestHeaders
	 * @covers ::lastRequest
	 * @uses \Kshabazz\Slib\HttpClient::__construct
	 * @uses \Kshabazz\Slib\HttpClient::send
	 * @uses \Kshabazz\Slib\HttpClient::__destruct
	 * @uses \Kshabazz\Slib\HttpClient::setResponseHeaders
	 * @uses \Kshabazz\Slib\HttpClient::populateResponseCode
	 * @interception www-example-com
	 */
	public function test_setHeaders()
	{
		$header = "test-header: test/1234";
		$request = new HttpClient();
		$request->setRequestHeaders([ $header ]);
		// Force last request property to be set.
		$request->send( $this->url );
		$headers = $request->lastRequest()[ 'header' ];
		$this->assertEquals( $header . "\r\n", $headers );
	}

	/**
	 * @covers ::post
	 * @uses \Kshabazz\Slib\HttpClient::lastRequest
	 * @uses \Kshabazz\Slib\HttpClient::__construct
	 * @uses \Kshabazz\Slib\HttpClient::send
	 * @uses \Kshabazz\Slib\HttpClient::__destruct
	 * @uses \Kshabazz\Slib\HttpClient::setResponseHeaders
	 * @uses \Kshabazz\Slib\HttpClient::populateResponseCode
	 * @interception ignore-test-valid-post-data
	 */
	public function test_setting_valid_post_data()
	{
		$testCase = [ 'test' => '1234' ];
		$request = new HttpClient();
		$request->post( $this->url, $testCase );
		$actual = $request->lastRequest()[ 'content' ];
		$this->assertEquals( 'test=1234', $actual );
	}

	/**
	 * @expectedException \PHPUnit_Framework_Error
	 * @expectedExceptionMessage must be of the type array
	 */
	public function test_setting_invalid_post_data()
	{
		$testCase = 'test 1234';
		$Request = new HttpClient();
		$Request->post( $this->url, $testCase );
	}

	/**
	 * @interception www-example-com
	 */
	public function test_getting_response_headers()
	{
		$http = new HttpClient();
		$http->send( $this->url );
		$responseHeaders = $http->responseHeaders();
		$this->assertEquals( "HTTP/1.0 200 OK", $responseHeaders[0] );
	}

	/**
	 * @interception www-example-com
	 */
	public function test_response_code()
	{
		$http = new HttpClient();
		$http->send( $this->url );
		$responseCode = $http->responseCode();
		$this->assertEquals( 200, $responseCode );
	}

	/**
	 * @interception response-code-null
	 */
	public function test_response_code_null()
	{
		$http = new HttpClient();
		$http->send( $this->url );
		$responseCode = $http->responseCode();
		$this->assertEquals( NULL, $responseCode );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function test_setting_empty_url()
	{
		$http = new HttpClient();
		$http->send( NULL );
	}

	/**
	 * @interception www-example-com
	 */
	public function test_getting_response_body()
	{
		$http = new HttpClient();
		$http->send( $this->url );
		$responseBody = $http->responseBody();
		$this->assertContains( 'Example Domain', $responseBody );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function test_setting_invalid_header_key()
	{
		$http = new HttpClient();
		$http->setRequestHeader( NULL, NULL );
	}

	/**
	 * @interception www-example-com
	 */
	public function test_setting_a_header()
	{
		$http = new HttpClient();
		$expected = 'test: 1234';
		$http->setRequestHeader( $expected );
		$http->send( $this->url );
		$requestHeader = $http->lastRequest()[ 'header' ];
		$this->assertContains( $expected, $requestHeader );
	}

	public function test_destroying()
	{
		try
		{
			$request = new HttpClient();
			$request->__destruct();
			$request->metaData();
		}
		catch ( \Exception $pError )
		{
			$expected = 'Undefined property: Kshabazz\Slib\HttpClient::$metaData';
			$this->assertContains( $expected, $pError->getMessage() );
		}
	}

	/**
	 * @interception ssl-www-example-com
	 */
	public function test_setting_ssl()
	{
		$expected = 'test: 1234';
		$this->httpClient->setRequestHeader( $expected );
		$this->httpClient->setSslOptions([
			'verify_peer_name' => FALSE,
		]);
		$this->httpClient->send( 'https://www.example.com/' );
		$requestHeader = $this->httpClient->lastRequest()[ 'header' ];
		$this->assertContains( $expected, $requestHeader );
	}
}
?>