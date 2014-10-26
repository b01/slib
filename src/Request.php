<?php namespace Kshabazz\Slib;
/**
 * Generic methods for making request over the internet.
 * @package Kshabazz\Slib
 */

/**
 * Class Request
 * Requires cURL extension
 *
 * @deprecated Please use Http class, which does not require cURL.
 * @package Kshabazz\Slib
 */
class Request
{
	const
		CONTENT_TYPE_HTTP = 'text/html; charset=utf-8',
		CONTENT_TYPE_JSON = 'application/json; charset=utf-8';

	private
		/** @var resource */
		$curl,
		/** @var array */
		$curlOptions,
		/** @var array */
		$headers,
		/** @var array */
		$postData,
		/** @var array */
		$requestInfo,
		/** @string */
		$url;

	/**
	 * Constructor to initialize the object.
	 */
	public function __construct()
	{
		$this->curlOptions = [
			\CURLOPT_RETURNTRANSFER => TRUE,
			\CURLINFO_HEADER_OUT => TRUE
		];
		$this->headers[ 'content-type' ] = self::CONTENT_TYPE_HTTP;
		$this->postData = NULL;
		$this->requestInfo = NULL;
		$this->responseText = NULL;
		$this->url = NULL;
		// init curl.
		$this->curl = \curl_init( $this->url );
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		// Closes cURL session and frees all resources. The cURL handle, $this->curl, is also deleted.
		\curl_close( $this->curl );
		// PHP does this auto, but I wanna make sure it gets done myself.
		unset(
			$this->curlOptions,
			$this->headers,
			$this->postData,
			$this->requestInfo,
			$this->url
		);
	}

	/**
	 * Get headers.
	 *
	 * @return array
	 */
	public function headers()
	{
		return $this->headers;
	}

	/**
	 * Currently set cURL options.
	 *
	 * @return array
	 */
	public function options()
	{
		return $this->curlOptions;
	}

	/**
	 * Get the HTTP response code of the request.
	 *
	 * @return int HTTP response code.
	 */
	public function responseCode()
	{
		if ( isArray($this->requestInfo) && \array_key_exists('http_code', $this->requestInfo) )
		{
			return $this->requestInfo[ 'http_code' ];
		}
		return NULL;
	}

	/**
	 * Send an HTTP request
	 *
	 * @param string $pUrl
	 * @return string|false see {\curl_exec
	 * @throws \InvalidArgumentException
	 */
	public function send( $pUrl )
	{
		if ( empty($pUrl) )
		{
			throw new \InvalidArgumentException( 'The URL is empty.' );
		}
		$this->url = $pUrl;
		// Set the URL.
		curl_setopt( $this->curl, \CURLOPT_URL, $pUrl );
		// Set headers.
		\curl_setopt( $this->curl, \CURLOPT_HTTPHEADER, $this->buildHeaderArray() );
		// Set curl options.
		\curl_setopt_array( $this->curl, $this->curlOptions );
		// Send the request and get a response.
		$responseText = \curl_exec( $this->curl );
		// Get information regarding the request.
		$this->requestInfo = \curl_getinfo( $this->curl );
		return $responseText;
	}

	/**
	 * Set cURL options.
	 *
	 * @param array $pOptions
	 * @return $this
	 */
	public function setCurlOptions( array $pOptions )
	{
		$this->curlOptions = $pOptions;

		return $this;
	}

	/**
	 * Set a header.
	 *
	 * @param $pKey
	 * @param $pValue
	 * @return $this
	 */
	public function setHeader( $pKey, $pValue )
	{
		if ( !is_string($pKey) )
		{
			throw new \InvalidArgumentException('Header key Must be a string.');
		}
		if ( !is_string($pValue) )
		{
			throw new \InvalidArgumentException('Header value Must be a string.');
		}
		$this->headers[ $pKey ] = $pKey . ':' . $pValue;

		return $this;
	}

	/**
	 * Set multiple headers. Any previously set headers will be updated with the new value.
	 *
	 * @param array $pHeaders
	 * @return Request
	 */
	public function setHeaders( array $pHeaders )
	{
		foreach ( $pHeaders as $key => $value )
		{
			$this->setHeader($key, $value);
		}

		return $this;
	}

	/**
	 * Set post data.
	 *
	 * @param array $pData
	 * @return Request
	 * @throws \InvalidArgumentException
	 */
	public function setPostData( array $pData )
	{
		$this->postData = $pData;
		$this->curlOptions[ \CURLOPT_POST ] = TRUE;
		$this->curlOptions[ \CURLOPT_POSTFIELDS ] = \http_build_query( $this->postData );

		return $this;
	}

	/**
	 * Get the URL.
	 *
	 * @return string
	 */
	public function url()
	{
		return $this->url;
	}

	/**
	 * Convert $this->headers into an array cURL can use.
	 *
	 * @return array
	 */
	private function buildHeaderArray()
	{
		$headers = [];
		foreach ( $this->headers as $key => $value )
		{
			$headers[] = $key . ': ' . $value;
		}

		return $headers;
	}
}
?>