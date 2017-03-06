<?php namespace Kshabazz\Slib;

use GuzzleHttp\Client;

/**
 * Class HttpService
 *
 * @package \Kshabazz\Slib
 */
abstract class HttpService
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /** @var array Default headers. */
    protected $defaultHeaders;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var \GuzzleHttp\Psr7\Request Information about the last request for debugging.
     */
    protected $lastRequest;

    /**
     * @var \GuzzleHttp\Psr7\Response Information about the last response for debugging.
     */
    protected $lastResponse;

    /**
     * Service constructor.
     *
     * @param \GuzzleHttp\Client $httpClient
     * @param string $baseUrl
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(Client $httpClient, $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
        $this->defaultHeaders = [];
    }

    /**
     * @return \GuzzleHttp\Psr7\Request Represents the request headers and body.
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return \GuzzleHttp\Psr7\Response Represents the response headers and body.
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Set headers to send in every request.
     *
     * Previous headers will be overwritten.
     *
     * @param array $headers
     */
    public function withHeaders(array $headers)
    {
        $this->defaultHeaders = $headers;
    }

    /**
     * Send a request
     *
     * @param string $method
     * @param string $endpoint
     * @param string $body
     * @param array $headers
     * @return null|string Return the response text.
     * @throws \Kshabazz\Slib\SlibException
     */
    protected function send($method, $endpoint = '', array $headers = null, $body = null)
    {
        $this->lastResponse = null;
        $url = $this->baseUrl . $endpoint;
        $headers['content-type'] = 'application/json';
        $options = ['headers' => array_merge($this->defaultHeaders, $headers)];

        if (!empty($body)) {
            $options['body'] = $body;
        }

        $this->lastRequest = $this->httpClient->request(
            $method,
            $url,
            $options
        );

        try {
            $this->lastResponse = $this->httpClient->send($this->lastRequest);
        } catch (\Exception $error) {
            throw new HttpServiceException(
                HttpServiceException::BAD_SERVICE_REQUEST,
                [$error->getMessage()]
            );
        }

        return $this->lastResponse;
    }
}