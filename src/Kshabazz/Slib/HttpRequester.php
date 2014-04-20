<?php namespace Kshabazz\Slib;
/**
 * Generic methods for retrieving HTML pages.
 * Developers: Khalifah K. Shabazz
 */

/**
 * Class HttpRequester
 * Requires cURL extension
 *
 * @package Kshabazz\Slib
 */
class HttpRequester
{
	protected
		$curl,
		$curlOptions,
		$headers,
		$postData,
		$requestInfo,
		$responseText,
		$url;

	/**
	 * Constructor
	 */
	public function __construct( $pUrl, array $pOptions = NULL )
	{
		$this->curlOptions = [ \CURLOPT_RETURNTRANSFER => TRUE ];
		$this->headers = [ 'Content-type: text/html;' ];
		$this->requestInfo = NULL;
		$this->responseText = NULL;
		$this->url = $pUrl;
		// init curl.
		$this->curl = curl_init( $this->url );
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		// PHP does this auto, but I wanna make sure it gets done myself.
		unset(
			$this->curl,
			$this->curlOptions,
			$this->headers,
			$this->requestInfo,
			$this->postData,
			$this->responseText,
			$this->url
		);
	}

	/**
	 * Allow using this object for multiple URL request.
	 *
	 * @param $pUrl
	 * @param $pOptions array replace previous curlOptions set, however, this will not unset any curlOptions in curl set by previous calls to send().
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function request( $pUrl, $pOptions = NULL )
	{
		$returnValue = NULL;
		if ( isString($pUrl) )
		{
			$this->url = $pUrl;
			// set curlOptions, replaces previous curlOptions.
			if ( isArray($pOptions) )
			{
				$this->curlOptions = $pOptions;
			}
			// Return the response text.
			$returnValue = $this->send();
		}
		else
		{
			throw new \Exception( "Invalid URL '{$pUrl}'" );
		}
		return $returnValue;
	}

	/**
	 * Get the HTTP response code of the request.
	 * @return int HTTP response code.
	 */
	public function responseCode()
	{
		if ( isArray($this->requestInfo) && array_key_exists("http_code", $this->requestInfo) )
		{
			return $this->requestInfo[ "http_code" ];
		}
		return NULL;
	}

	/**
	 * Send an HTTP request
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function send()
	{
		//
		$returnValue = NULL;
		if ( !empty($this->url) )
		{
			// set any curl curlOptions needed.
			curl_setopt_array( $this->curl, $this->curlOptions );
			// Send the request and get a response.
			$responseText = curl_exec( $this->curl );
			// get the status of the call
			$this->requestInfo = curl_getinfo( $this->curl );
			curl_close( $this->curl );
			if ( !empty($responseText) )
			{
				$returnValue = $responseText;
			}
		}
		else
		{
			throw new \Exception( "Invalid URL: '{$this->url}'" );
		}
		return $returnValue;
	}

	/**
	 * Set the URL of the request.
	 */
	public function setUrl( $pUrl )
	{
		$this->url = $pUrl;
	}

	/**
	 * Set the URL of the request.
	 */
	public function headers( $pHeaders )
	{
		$this->headers = $pHeaders;
		$this->curlOptions[ \CURLOPT_HTTPHEADER ] = $this->headers;
		return $this->headers;
	}

	/**
	 * Currently set cURL options.
	 * @return array
	 */
	public function options()
	{
		return $this->curlOptions;
	}

	/**
	 * Set the URL of the request.
	 * @param $pData
	 * @throws \InvalidArgumentException
	 */
	public function setPostData( $pData )
	{
		if ( !is_string($pData) )
		{
			throw new \InvalidArgumentException( 'POST data must be a string.' );
		}
		$this->postData = $pData;
		$this->curlOptions[ \CURLOPT_POST ] = TRUE;
		$this->curlOptions[ \CURLOPT_POSTFIELDS ] = $this->postData;
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