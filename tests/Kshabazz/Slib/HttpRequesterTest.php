<?php namespace Kshabazz\Slib\Test;
/**
 * Developers: Khalifah K. Shabazz
 */
use Kshabazz\Slib\HttpRequester;
/**
 * Class HttpRequesterTest
 *
 * @package Kshabazz\Slib\Test
 */
class HttpRequesterTest extends \PHPUnit_Framework_TestCase
{
	private
		$url;

	public function setUp()
	{
		$this->url = 'http://www.example.com';
	}

	public function test_initialization()
	{
		$httpRequester = new HttpRequester( NULL );
		$this->assertInstanceOf(
			'\Kshabazz\Slib\HttpRequester',
			$httpRequester,
			'Could not initialize \Kshabazz\Slib\HttpRequester object.'
		);
	}

	public function test_getting_the_same_url_put_in()
	{
		$httpRequester = new HttpRequester( $this->url );
		$url = $httpRequester->url();
		$this->assertEquals( $this->url, $url, 'URL retrieved is not the same as the one put in.' );
	}

	public function test_updating_url_with_setUrl()
	{
		$secondUrl = 'http://www.kshabazz.net';
		$httpRequester = new HttpRequester( $this->url );
		$url = $httpRequester->url();
		$this->assertEquals( $this->url, $url, 'URL retrieved is not the same as the one put in.' );
		$httpRequester->setUrl( $secondUrl );
		$url = $httpRequester->url( $secondUrl );
		$this->assertEquals( $secondUrl, $url, 'The second URL retrieved is not the same as the second URL set.' );
	}

	public function test_setting_headers()
	{
		$testCase = 'Content-type: text/xml';
		$httpRequester = new HttpRequester( NULL );
		$httpRequester->headers([ $testCase ]);
		$curlOptions = $httpRequester->options();
		$this->assertEquals(
			$testCase,
			$curlOptions[ \CURLOPT_HTTPHEADER ][ 0 ],
			'Could not retrieve header.'
		);
	}

	public function test_setting_post_data()
	{
		$testCase = 'test 1234';
		$httpRequester = new HttpRequester( NULL );
		$httpRequester->setPostData( $testCase );
		$curlOptions = $httpRequester->options();
		// Verify POST flag was set.
		$this->assertTrue(
			$curlOptions[ \CURLOPT_POST ],
			'Could not set post flag.'
		);
		// Verify POST data was set.
		$this->assertEquals(
			$testCase,
			$curlOptions[ \CURLOPT_POSTFIELDS ],
			'Could not set post data.'
		);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage POST data must be a string.
	 */
	public function test_setting_invalid_post_data()
	{
		$testCase = 'test 1234';
		$httpRequester = new HttpRequester( NULL );
		$httpRequester->setPostData([ $testCase ]);
	}
	/**
	 * @vcr google-request.yml
	 */
	public function test_send()
	{
		$cassettePath = \VCR\VCR::configure()->getCassettePath();
		$http = new \Kshabazz\Slib\HttpRequester( $this->url );
		$http->send();
		$this->assertEquals(
			200,
			$http->responseCode(),
			'Failed to retrieve ' . $this->url
		);
	}
}
