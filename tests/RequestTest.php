<?php namespace Kshabazz\Test\Slib;
/**
 * Developers: Khalifah K. Shabazz
 */

use Kshabazz\Slib\Request;

/**
 * Class RequestTest
 *
 * @package \Kshabazz\Test\Slib
 */
class RequestTest extends \PHPUnit_Framework_TestCase
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
		$request = new Request();
		$this->assertInstanceOf(
			'\Kshabazz\Slib\Request',
			$request,
			'Could not initialize \Kshabazz\Slib\Request object.'
		);
		// url should be intialized to NULL.
		$this->assertNull($request->url());
		// Headers should be initialized to HTML ccntent-type.
		$headers = $request->headers();
		$this->assertEquals( Request::CONTENT_TYPE_HTTP, $headers['content-type'] );
	}

	public function test_getting_the_same_url_put_in()
	{
		$request = new Request();
		$request->send( $this->url );
		$url = $request->url();
		$this->assertEquals( $this->url, $url, 'The URL was not set correctly.' );
	}

	public function test_updating_url_with_setUrl()
	{
		$secondUrl = 'http://www.kshabazz.net';
		$request = new Request();
		// Set URL 1.
		$request->send( $this->url );
		$url = $request->url( $secondUrl );
		$this->assertEquals( $this->url, $url, 'URL retrieved is not the same as the one put in.' );
		// Set URL 2.
		$request->send( $secondUrl );
		$url = $request->url( $secondUrl );
		$this->assertEquals( $secondUrl, $url, 'The second URL retrieved is not the same as the second URL set.' );
	}

	public function test_setHeaders()
	{
		$key = 'test-header';
		$testCase = [ $key => 'test/1234' ];
		$request = new Request();
		$request->setHeaders( $testCase );
		$headers = $request->headers();
		$this->assertArrayHasKey( $key, $headers, 'Header not set properly.' );
	}

	public function test_setting_post_data()
	{
		$testCase = [ 'test' => '1234' ];
		$request = new Request();
		$request->setPostData( $testCase );
		$curlOptions = $request->options();
		// Verify POST flag was set.
		$this->assertTrue(
			$curlOptions[ \CURLOPT_POST ],
			'Could not set post flag.'
		);
		// Verify POST data was set.
		$this->assertEquals(
			\http_build_query( $testCase ),
			$curlOptions[ \CURLOPT_POSTFIELDS ],
			'Could not set post data.'
		);
	}

	public function test_send()
	{
		$http = new \Kshabazz\Slib\Request();
		$responseText = $http->send( $this->url );
		$this->assertEquals( 200, $http->responseCode(), 'Failed to retrieve ' . $this->url );
//		$this->assertNotFalse( $responseText, 'Retrieving response failed.' );
	}
}
?>