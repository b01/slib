<?php namespace Kshabazz\Slib\Tests\Mocks;

use Kshabazz\Slib\HttpService;

/**
 * Class MockHttpService
 *
 * @package \Kshabazz\Slib\Tests\Mocks
 */
class MockHttpService extends HttpService
{
    public function doSend($method, $endpoint, $headers, $body)
    {
        return parent::send($method, $endpoint, $headers, $body);
    }
}
