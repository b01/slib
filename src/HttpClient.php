<?php namespace Kshabazz\Slib;
/**
 * Generic methods for making HTTP request over the internet.
 * @package Kshabazz\Slib
 */

/**
 * Class Request
 *
 * @package Kshabazz\Slib
 */
class HttpClient
{
	const
		CONTENT_TYPE_GET = 'content-type: text/html; charset=utf-8',
		CONTENT_TYPE_JSON = 'content-type: application/json; charset=utf-8',
		CONTENT_TYPE_POST = 'content-type: application/x-www-form-urlencoded';

	private
		/** @var null|array Copy of the last request sent. */
		$lastRequest,
		/** @var array */
		$metaData,
		/** @var array */
		$postData,
		/** @var string Request body. */
		$requestContent,
		/** @var array Request headers. */
		$requestHeaders,
		/** @var string Method use to send the request. */
		$requestMethod,
		/** @var int HTTP response code. */
		$responseCode,
		/** @var string Response body returned after making a request. */
		$responseContent,
		/** @var array Response headers. */
		$responseHeaders,
		/** @var array */
		$sslOptions,
		/** @var string URL of the request. */
		$url;

	/**
	 * Constructor to initialize the object.
	 */
	public function __construct()
	{
		$this->lastRequest = NULL;
		$this->metaData = NULL;
		$this->postData = NULL;
		$this->requestContent = NULL;
		$this->requestHeaders = [ 'content-type' => self::CONTENT_TYPE_GET ];
		$this->requestMethod = 'GET';
		$this->responseCode = NULL;
		$this->responseContent = NULL;
		$this->responseHeaders = NULL;
		$this->ssl = NULL;
		$this->url = NULL;
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		// PHP does this auto, but I wanna make sure it gets done myself.
		unset(
			$this->metaData,
			$this->postData,
			$this->requestHeaders,
			$this->responseHeaders,
			$this->responseContent,
			$this->url
		);
	}

	/**
	 * Get all info sent in the last request.
	 *
	 * @return null
	 */
	public function lastRequest()
	{
		return $this->lastRequest;
	}
	/**
	 * Get the meta data for the last request.
	 * @return null
	 */
	public function metaData()
	{
		return $this->metaData;
	}

	/**
	 * Make an HTTP POST request.
	 *
	 * @param $pUrl
	 * @param array $pPostData
	 * @return bool
	 */
	public function post( $pUrl, array $pPostData )
	{
		// Set POST context.
		$this->requestMethod = 'POST';
		$this->requestHeaders[ 'content-type' ] = self::CONTENT_TYPE_POST;
		// Set post data.
		$this->requestContent = \http_build_query( $pPostData, '&' );
		return $this->send( $pUrl );
	}

	/**
	 * Get response headers.
	 *
	 * @return array
	 */
	public function responseHeaders()
	{
		return $this->responseHeaders;
	}

	/**
	 * Get the response text of the request.
	 *
	 * @return string|null
	 */
	public function responseBody()
	{
		return $this->responseContent;
	}

	/**
	 * Get the HTTP response code of the request.
	 *
	 * @return int HTTP response code.
	 * @throws \Exception
	 */
	public function responseCode()
	{
		return $this->responseCode;
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
			throw new \InvalidArgumentException( 'The URL is not set.' );
		}
		// Set the URL.
		$this->url = $pUrl;
		// Keep a record of what was request for debugging.
		$this->lastRequest = [
			'method' => $this->requestMethod,
			'header' => \implode( "\r\n", $this->requestHeaders ) . "\r\n",
			'content' => $this->requestContent
		];
		$contextOptions = [ 'http' => $this->lastRequest ];
		if ( \is_array($this->sslOptions) )
		{
			$contextOptions[ 'ssl' ] = $this->sslOptions;
		}
		// Set the request headers.
		$context = \stream_context_create( $contextOptions );
		// Make the request.
		$resource = \fopen( $this->url, 'r', FALSE, $context );
		// Get information regarding the request.
		$this->metaData = \stream_get_meta_data( $resource );
		// Populate the response headers.
		$this->setResponseHeaders( $this->metaData );
		$this->populateResponseCode();
		// Set the response body.
		$this->responseContent = \stream_get_contents( $resource );
		// Release the resource handle.
		\fclose( $resource );
		$resource = NULL;
		return TRUE;
	}

	/**
	 * Set a request header. A new line will be appended to the header.
	 *
	 * @example: 'content-type: text/html; charset=utf-8'
	 * @param string $pHeader
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setRequestHeader( $pHeader )
	{
		if ( !\is_string($pHeader) )
		{
			throw new \InvalidArgumentException( 'Header key Must be a string.' );
		}
		// Not separating headers into key => value pairs allows the same header to be set multiple times.
		// Which is O.K. according to HTTP/1.1 RFC 2616 @ http://www.ietf.org/rfc/rfc2616.txt Section 4.2.
		$this->requestHeaders[] = $pHeader . "\r\n";

		return $this;
	}

	/**
	 * Set request headers, removing any previously set request headers.

	 *
*@example setRequestHeaders([ "content-type: text/html; charset=utf-8\r\n" ]);f
	 * @param array $pHeaders
	 * @return HttpClient
	 * @throws \InvalidArgumentException
	 */
	public function setRequestHeaders( array $pHeaders )
	{
		$this->requestHeaders = $pHeaders;
		return $this;
	}

	/**
	 * Set SSL options.
	 *
	 * @param array $pOptions
	 */
	public function setSslOptions( array $pOptions )
	{
		$this->sslOptions = $pOptions;
	}

	/**
	 * Get the HTTP response code of the request.
	 *
	 * @return int HTTP response code.
	 * @throws \Exception
	 */
	private function populateResponseCode()
	{
		$hasStatus = \preg_match( '#HTTP/... (\d+) .*$#', $this->responseHeaders[0], $results );
		if ( $hasStatus )
		{
			$this->responseCode = ( int ) $results[ 1 ];
		}
		return $this;
	}

	private function setResponseHeaders( $metaData )
	{
		if ( !\is_array($metaData) || !\array_key_exists('wrapper_data', $metaData) )
		{
			throw new \Exception( 'No response headers are set!' );
		}
		$this->responseHeaders = $metaData[ 'wrapper_data' ];
	}
}
?>