<?php namespace Kshabazz\Slib;

/**
 * Class HttpService
 *
 * @package \Kshabazz\Slib
 */
class HttpService
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

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var \GuzzleHttp\Message\Request Information about the last request for debugging.
     */
    protected $lastRequest;

    /**
     * @var \GuzzleHttp\Message\Response Information about the last response for debugging.
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
    public function __construct(
        Client $httpClient,
        $baseUrl,
        $clientId,
        $clientSecret
    ) {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return \GuzzleHttp\Message\Request Represents the request headers and body.
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return \GuzzleHttp\Message\Response Represents the response headers and body.
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Send a request
     *
     * @param string $method
     * @param string $endpoint
     * @param string $body
     * @param array $headers
     * @return null|string Return the response text.
     * @throws \Venture\RateGrabber\RateGrabberException
     */
    protected function send($method, $endpoint = '', array $headers = null, $body = null)
    {
        $this->lastResponse = null;
        $url = $this->baseUrl . $endpoint;
        $headers['content-type'] = 'application/json';
        $headers['client_id'] = $this->clientId;
        $headers['client_secret'] = $this->clientSecret;
        $options = ['headers' => $headers];

        if (!empty($body)) {
            $options['body'] = $body;
        }

        $this->lastRequest = $this->httpClient->createRequest(
            $method,
            $url,
            $options
        );

        try {
            $this->lastResponse = $this->httpClient->send($this->lastRequest);
        } catch (\Exception $error) {
            throw new RateGrabberException(
                RateGrabberException::BAD_SERVICE_REQUEST,
                [__CLASS__, $error->getMessage()]
            );
        }

        return $this->lastResponse;
    }
}