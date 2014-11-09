<?php namespace Kshabazz\Test\Slib;

use \Kshabazz\Slib\Http;

/**
 * Class RequestTest
 *
 * @package \Kshabazz\Test\Slib
 * @coversDefaultClass \Kshabazz\Slib\Http
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
	private
		/** @var string */
		$url;

	public function setUp()
	{
		$this->url = 'http://www.example.com';
	}

	public function test_initialization()
	{
		$http = new Http();
		$this->assertInstanceOf( '\\Kshabazz\\Slib\\Http', $http );
	}

	/**
	 * @covers ::setRequestHeaders
	 * @covers ::lastRequest
	 * @uses \Kshabazz\Slib\Http::__construct
	 * @uses \Kshabazz\Slib\Http::send
	 * @uses \Kshabazz\Slib\Http::__destruct
	 * @uses \Kshabazz\Slib\Http::setResponseHeaders
	 * @uses \Kshabazz\Slib\Http::populateResponseCode
	 * @interception ignore-example-com
	 */
	public function test_setHeaders()
	{
		$header = "test-header: test/1234";
		$request = new Http();
		$request->setRequestHeaders([ $header ]);
		// Force last request property to be set.
		$request->send( $this->url );
		$headers = $request->lastRequest()[ 'header' ];
		$this->assertEquals( $header . "\r\n", $headers );
	}

	/**
	 * @covers ::post
	 * @uses \Kshabazz\Slib\Http::lastRequest
	 * @uses \Kshabazz\Slib\Http::__construct
	 * @uses \Kshabazz\Slib\Http::send
	 * @uses \Kshabazz\Slib\Http::__destruct
	 * @uses \Kshabazz\Slib\Http::setResponseHeaders
	 * @uses \Kshabazz\Slib\Http::populateResponseCode
	 * @interception ignore-test-valid-post-data
	 */
	public function test_setting_valid_post_data()
	{
		$testCase = [ 'test' => '1234' ];
		$request = new Http();
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
		$Request = new Http();
		$Request->post( $this->url, $testCase );
	}

	/**
	 * @interception ignore-example-com
	 */
	public function test_getting_response_headers()
	{
		$http = new Http();
		$http->send( $this->url );
		$responseHeaders = $http->responseHeaders();
		$this->assertEquals( "HTTP/1.0 200 OK", $responseHeaders[0] );
	}

	/**
	 * @interception ignore-example-com
	 */
	public function test_response_code()
	{
		$http = new Http();
		$http->send( $this->url );
		$responseCode = $http->responseCode();
		$this->assertEquals( 200, $responseCode );
	}

	/**
	 * @interception test-response-code-null
	 */
	public function test_response_code_null()
	{
		$http = new Http();
		$http->send( $this->url );
		$responseCode = $http->responseCode();
		$this->assertEquals( NULL, $responseCode );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function test_setting_empty_url()
	{
		$http = new Http();
		$http->send( NULL );
	}

	/**
	 * @interception ignore-example-com
	 */
	public function test_getting_response_body()
	{
		$http = new Http();
		$http->send( $this->url );
		$responseBody = $http->responseBody();
		$this->assertContains( 'Example Domain', $responseBody );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function test_setting_invalid_header_key()
	{
		$http = new Http();
		$http->setRequestHeader( NULL, NULL );
	}

	/**
	 * @interception ignore-example-com
	 */
	public function test_setting_a_header()
	{
		$http = new Http();
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
			$request = new Http();
			$request->__destruct();
			$request->metaData();
		}
		catch ( \Exception $pError )
		{
			$expected = 'Undefined property: Kshabazz\Slib\Http::$metaData';
			$this->assertContains( $expected, $pError->getMessage() );
		}
	}
}
?>