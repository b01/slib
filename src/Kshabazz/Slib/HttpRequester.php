<?php namespace Kshabazz\Slib;
/**
 * Generic methods for retrieving HTML pages.
 * Developers: Khalifah K. Shabazz
 */

/**
 * Class HttpRequester
 * For quick no-real-setup HTTP request, all you need is a URL and to set the content type, and optional POST data.
 * Requires cURL extension
 *
 * @package Kshabazz\Slib
 */
class HttpRequester
{
	const
		TYPE_HTTP = 'text/html; charset=utf-8',
		TYPE_JSON = 'application/json; charset=utf-8';

	protected
		$headers,
		$httpClient,
		$postData,
		$requestInfo,
		$responseText,
		$statusCode,
		$url;

	/**
	 * Constructor
	 */
	public function __construct( $pUrl, array $pHeaders = ['content-type' => self::TYPE_HTTP] )
	{
		$this->headers = $pHeaders;
		// Init an HTTP client to send a request.
		$this->httpClient = new \GuzzleHttp\Client();
		$this->postData = NULL;
		$this->requestInfo = NULL;
		$this->responseText = NULL;
		$this->url = $pUrl;
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		// PHP does this auto, but I wanna make sure it gets done myself.
		unset(
			$this->headers,
			$this->httpClient,
			$this->requestInfo,
			$this->postData,
			$this->responseText,
			$this->url
		);
	}

	/**
	 * Set request headers.
	 *
	 * @param array $pHeaders
	 * @return $this
	 */
	public function addHeaders( array $pHeaders )
	{
		$lowerCaseKeys = array_change_key_case( $pHeaders );
		$this->headers = array_merge( $this->headers, $lowerCaseKeys );
		return $this;
	}

	/**
	 * Get the headers.
	 *
	 * @return array
	 */
	public function headers()
	{
		return $this->headers;
	}

	/**
	 * Get any data set for the request.
	 *
	 * @return null|array
	 */
	public function postData()
	{
		return $this->postData;
	}

	/**
	 * Get the HTTP response code of the request.
	 *
	 * @return int|NULL HTTP response code.
	 */
	public function responseCode()
	{
		return $this->statusCode;
	}

	/**
	 * Send an HTTP request
	 *
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function send()
	{
		if ( empty($this->url) )
		{
			throw new \Exception( "URL was not set: '{$this->url}'" );
		}
		// Clear previous response text.
		$this->responseText = NULL;
		// Decide if we need to POST or GET the request.
		if ( is_array($this->postData) )
		{
			$request = $this->httpClient->createRequest( 'POST', $this->url );
			$request->setBody($this->postData);
		}
		else  // Do a GET
		{
			$request = $this->httpClient->createRequest( 'GET', $this->url );
		}

		// Add any additional headers.
		$request->addHeaders( $this->headers );
		$response = $this->httpClient->send( $request );
		$this->statusCode = (int) $response->getStatusCode();
		if ( $this->statusCode === 200 )
		{
			// Always return as a string so we can store it, we can decode ourselves.
			$this->responseText = (string) $response->getBody();
		}
		return $this->responseText;
	}

	/**
	 * Set any data you wish to POST to the request.
	 *
	 * @param array $pPostData
	 * @return $this
	 */
	public function setPostData( array $pPostData )
	{
		$this->postData = $pPostData;
		return $this;
	}

	/**
	 * Set the URL of the request.
	 */
	public function setUrl( $pUrl )
	{
		$this->url = $pUrl;
		return $this;
	}

	/**
	 * Get the URL of the request.
	 *
	 * @return {string|NULL}
	 */
	public function url()
	{
		return $this->url;
	}
}
?>